<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockAdjustment;
use App\Models\SupplierReturn;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierReturnController extends Controller
{
    public function index()
    {
        $recentReturns = SupplierReturn::with(['product', 'supplier'])
            ->latest()
            ->paginate(10);

        $completedCount = SupplierReturn::where('status', 'completed')->count();
        $completedQty = SupplierReturn::where('status', 'completed')->sum('quantity');
        
        $rejectedCount = SupplierReturn::where('status', 'rejected')->count();
        $rejectedQty = SupplierReturn::where('status', 'rejected')->sum('quantity');

        $supplierSummary = SupplierReturn::select(
                'supplier_id',
                DB::raw('COUNT(*) as returns_count'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->with('supplier')
            ->groupBy('supplier_id')
            ->get()
            ->map(function ($item) {
                return (object)[
                    'supplier_name' => $item->supplier->supplier_name ?? 'Unknown',
                    'returns_count' => $item->returns_count,
                    'total_quantity' => $item->total_quantity,
                ];
            });

        return view('admin.supplier-returns.index', compact(
            'recentReturns',
            'completedCount',
            'completedQty',
            'rejectedCount',
            'rejectedQty',
            'supplierSummary'
        ));
    }

    public function byStatus($status)
    {
        $returns = SupplierReturn::with(['product', 'supplier'])
            ->where('status', $status)
            ->latest()
            ->paginate(20);

        $statusLabel = ucfirst($status);

        return view('admin.supplier-returns.status', compact('returns', 'status', 'statusLabel'));
    }

    public function bySupplier($supplier)
    {
        $recentReturns = SupplierReturn::with(['product', 'supplier'])
            ->where('supplier_id', $supplier)
            ->latest()
            ->paginate(20);

        $supplierSummary = collect();

        return view('admin.supplier-returns.index', compact(
            'recentReturns',
            'supplierSummary'
        ));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        
        // FIX: Get products with their adjustment pool balances
        $products = Product::where('is_active', true)
            ->with(['stockAdjustments' => function($query) {
                $query->select('product_id', 'adjustment_type', DB::raw('SUM(quantity) as total'))
                    ->groupBy('product_id', 'adjustment_type');
            }])
            ->get();

        return view('admin.supplier-returns.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|in:empty,defective,customer_damaged,other',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        try {
            DB::beginTransaction();

            // FIX: Map reason to adjustment type
            $adjustmentTypeMap = [
                'empty' => 'return_in',
                'defective' => 'damage_out',
                'customer_damaged' => 'customer_damaged',
                'other' => null, // Other deducts from product stock directly
            ];

            $adjustmentType = $adjustmentTypeMap[$validated['reason']];

            // FIX: For non-"other" reasons, check and deduct from existing adjustment pool
            if ($adjustmentType !== null) {
                // Get total available pool for this product and type
                $availablePool = StockAdjustment::where('product_id', $validated['product_id'])
                    ->where('adjustment_type', $adjustmentType)
                    ->sum('quantity');

                if ($availablePool <= 0) {
                    throw new \Exception('No available ' . ucfirst(str_replace('_', ' ', $adjustmentType)) . ' pool for this product. Please record an adjustment first.');
                }

                if ($validated['quantity'] > $availablePool) {
                    throw new \Exception('Return quantity exceeds available pool. Available: ' . $availablePool . ', Requested: ' . $validated['quantity']);
                }

                // FIX: Deduct from existing adjustments (FIFO - oldest first)
                $remainingQty = $validated['quantity'];
                $adjustments = StockAdjustment::where('product_id', $validated['product_id'])
                    ->where('adjustment_type', $adjustmentType)
                    ->where('quantity', '>', 0)
                    ->orderBy('adjustment_date', 'asc')
                    ->get();

                foreach ($adjustments as $adjustment) {
                    if ($remainingQty <= 0) break;

                    $deductQty = min($remainingQty, $adjustment->quantity);
                    
                    // Update the existing adjustment record
                    $adjustment->decrement('quantity', $deductQty);
                    
                    // If quantity reaches 0, soft delete or keep at 0
                    if ($adjustment->quantity <= 0) {
                        $adjustment->quantity = 0;
                        $adjustment->save();
                    }

                    $remainingQty -= $deductQty;
                }
            }

            // Create supplier return record
            $return = SupplierReturn::create([
                'product_id' => $validated['product_id'],
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id(),
                'quantity' => $validated['quantity'],
                'reason' => $validated['reason'],
                'return_date' => now()->toDateString(),
                'notes' => $validated['notes'] ?? null,
                'status' => 'completed',
            ]);

            // Log stock movement
            $stockBefore = $product->stock_quantity;
            
            if ($validated['reason'] === 'other') {
                // Other: Deduct from product stock directly
                $product->decrement('stock_quantity', $validated['quantity']);
                
                StockMovement::create([
                    'product_id' => $product->product_id,
                    'movement_type' => 'supplier_return',
                    'quantity' => -$validated['quantity'],
                    'reference_type' => 'supplier_return',
                    'reference_id' => $return->return_id,
                    'stock_before' => $stockBefore,
                    'stock_after' => $product->stock_quantity,
                    'user_id' => Auth::id(),
                    'remarks' => 'Supplier Return - Reason: Other - Product stock deducted by ' . $validated['quantity'],
                ]);
            } else {
                // Return In / Damaged / Customer Damaged: No stock change, only pool deduction
                StockMovement::create([
                    'product_id' => $product->product_id,
                    'movement_type' => 'adjustment',
                    'quantity' => 0,
                    'reference_type' => $adjustmentType,
                    'reference_id' => $return->return_id,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockBefore,
                    'user_id' => Auth::id(),
                    'remarks' => 'Supplier Return - ' . ucfirst(str_replace('_', ' ', $adjustmentType)) . ' pool deducted by ' . $validated['quantity'] . ' (No product stock change)',
                ]);
            }

            DB::commit();

            return redirect()->route('admin.supplier-returns.index')
                ->with('success', 'Supplier return recorded successfully. ' . ($adjustmentType ? ucfirst(str_replace('_', ' ', $adjustmentType)) . ' pool updated.' : 'Product stock deducted.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show(SupplierReturn $return)
    {
        return view('admin.supplier-returns.show', compact('return'));
    }

    public function updateStatus(Request $request, SupplierReturn $return)
    {
        $validated = $request->validate([
            'return_status' => 'required|in:completed,rejected',
        ]);

        $oldStatus = $return->status;
        $newStatus = $validated['return_status'];

        DB::transaction(function () use ($return, $oldStatus, $newStatus) {
            
            $return->update([
                'status' => $newStatus
            ]);

            // FIX: Restore pool when rejected
            if ($oldStatus === 'completed' && $newStatus === 'rejected') {
                $adjustmentTypeMap = [
                    'empty' => 'return_in',
                    'defective' => 'damage_out',
                    'customer_damaged' => 'customer_damaged',
                ];

                if (isset($adjustmentTypeMap[$return->reason])) {
                    // Restore the pool by creating a positive adjustment
                    StockAdjustment::create([
                        'product_id' => $return->product_id,
                        'adjustment_type' => $adjustmentTypeMap[$return->reason],
                        'quantity' => $return->quantity,
                        'reason' => 'Restored: Rejected supplier return #' . $return->return_id,
                        'reference_type' => 'supplier_return_restore',
                        'reference_id' => $return->return_id,
                        'user_id' => Auth::id(),
                        'adjustment_date' => now(),
                    ]);
                } elseif ($return->reason === 'other') {
                    // Restore product stock
                    $product = Product::findOrFail($return->product_id);
                    $product->increment('stock_quantity', $return->quantity);
                }
            }

            // FIX: Re-deduct when reactivated
            if ($oldStatus === 'rejected' && $newStatus === 'completed') {
                $adjustmentTypeMap = [
                    'empty' => 'return_in',
                    'defective' => 'damage_out',
                    'customer_damaged' => 'customer_damaged',
                ];

                if (isset($adjustmentTypeMap[$return->reason])) {
                    // Re-deduct from pool (same logic as store)
                    $adjustmentType = $adjustmentTypeMap[$return->reason];
                    $remainingQty = $return->quantity;
                    
                    $adjustments = StockAdjustment::where('product_id', $return->product_id)
                        ->where('adjustment_type', $adjustmentType)
                        ->where('quantity', '>', 0)
                        ->orderBy('adjustment_date', 'asc')
                        ->get();

                    foreach ($adjustments as $adjustment) {
                        if ($remainingQty <= 0) break;
                        $deductQty = min($remainingQty, $adjustment->quantity);
                        $adjustment->decrement('quantity', $deductQty);
                        if ($adjustment->quantity <= 0) {
                            $adjustment->quantity = 0;
                            $adjustment->save();
                        }
                        $remainingQty -= $deductQty;
                    }
                } elseif ($return->reason === 'other') {
                    // Re-deduct from product stock
                    $product = Product::findOrFail($return->product_id);
                    $product->decrement('stock_quantity', $return->quantity);
                }
            }
        });

        return redirect()->route('admin.supplier-returns.index')
            ->with('success', 'Status updated successfully.');
    }
}