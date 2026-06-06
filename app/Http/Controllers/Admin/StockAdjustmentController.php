<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    private $adjustmentTypes = [
        'return_in' => [
            'label' => 'Return In',
            'description' => 'Customer returns (empty/damaged products - no stock change)',
            'icon' => 'fa-undo-alt',
            'bg_color' => 'bg-blue-50',
            'border_color' => 'border-blue-200',
            'text_color' => 'text-blue-700',
            'icon_color' => 'text-blue-600',
            'badge_color' => 'bg-blue-100',
        ],
        // FIX: damage_out now DEDUCTS stock (for warehouse/physical damage)
        'damage_out' => [
            'label' => 'Damaged',
            'description' => 'Broken or damaged stock - DEDUCTS from product stock',
            'icon' => 'fa-exclamation-triangle',
            'bg_color' => 'bg-red-50',
            'border_color' => 'border-red-200',
            'text_color' => 'text-red-700',
            'icon_color' => 'text-red-600',
            'badge_color' => 'bg-red-100',
        ],
        // FIX: New type - customer damaged return (no stock change)
        'customer_damaged' => [
            'label' => 'Customer Damaged',
            'description' => 'Customer returned damaged item - Record only (no stock change)',
            'icon' => 'fa-user-times',
            'bg_color' => 'bg-orange-50',
            'border_color' => 'border-orange-200',
            'text_color' => 'text-orange-700',
            'icon_color' => 'text-orange-600',
            'badge_color' => 'bg-orange-100',
        ],
        'lost' => [
            'label' => 'Lost / Missing',
            'description' => 'Inventory discrepancies & missing items - Record only (no stock change)',
            'icon' => 'fa-question-circle',
            'bg_color' => 'bg-gray-50',
            'border_color' => 'border-gray-200',
            'text_color' => 'text-gray-700',
            'icon_color' => 'text-gray-600',
            'badge_color' => 'bg-gray-100',
        ],
    ];

    public function index()
    {
        $typeStats = [];
        foreach (array_keys($this->adjustmentTypes) as $type) {
            $typeStats[$type] = [
                'count' => StockAdjustment::where('adjustment_type', $type)->count(),
                'total_quantity' => StockAdjustment::where('adjustment_type', $type)->sum('quantity'),
                'last_adjustment' => StockAdjustment::where('adjustment_type', $type)->latest()->first(),
            ];
        }

        $recentAdjustments = StockAdjustment::with(['product', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.adjustments.index', compact('typeStats', 'recentAdjustments'));
    }

    public function byType($type)
    {
        if (!isset($this->adjustmentTypes[$type])) {
            abort(404);
        }

        $typeConfig = $this->adjustmentTypes[$type];

        $adjustments = StockAdjustment::with(['product', 'user'])
            ->where('adjustment_type', $type)
            ->latest()
            ->paginate(20);

        $stats = [
            'total_count' => StockAdjustment::where('adjustment_type', $type)->count(),
            'total_quantity' => StockAdjustment::where('adjustment_type', $type)->sum('quantity'),
            'this_month' => StockAdjustment::where('adjustment_type', $type)
                ->whereMonth('adjustment_date', now()->month)
                ->whereYear('adjustment_date', now()->year)
                ->count(),
        ];

        return view('admin.adjustments.by-type', compact('adjustments', 'type', 'typeConfig', 'stats'));
    }

    public function create(Request $request)
    {
        $products = Product::where('is_active', true)->get();
        $sales = Sale::with(['product', 'user'])->latest()->limit(50)->get();
        
        $preSelectedType = $request->get('type', old('adjustment_type', ''));

        return view('admin.adjustments.create', compact('products', 'sales', 'preSelectedType'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            // FIX: Added customer_damaged to validation
            'adjustment_type' => 'required|in:return_in,damage_out,customer_damaged,lost',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'reference_id' => 'nullable|integer',
            'adjustment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        DB::transaction(function () use ($validated) {
            $product = Product::find($validated['product_id']);
            $stockBefore = $product->stock_quantity;

            // FIX: damage_out now DEDUCTS stock, customer_damaged has NO stock change
            $quantityChange = match($validated['adjustment_type']) {
                'return_in' => 0,
                'damage_out' => -$validated['quantity'], // FIX: Now deducts stock
                'customer_damaged' => 0, // FIX: New type - no stock change
                'lost' => 0,
                default => -$validated['quantity'],
            };

            // FIX: Check stock only for damage_out (the only type that deducts stock)
            if ($quantityChange < 0 && $stockBefore < abs($quantityChange)) {
                throw new \Exception('Insufficient stock for this adjustment.');
            }

            // Always create new record
            $adjustment = StockAdjustment::create($validated);

            // FIX: Update product stock for damage_out (deducts stock)
            if ($quantityChange !== 0) {
                $product->increment('stock_quantity', $quantityChange);
            }

            // Log stock movement
            StockMovement::create([
                'product_id' => $validated['product_id'],
                'movement_type' => 'adjustment',
                'quantity' => $quantityChange,
                'reference_type' => $validated['adjustment_type'],
                'reference_id' => $adjustment->adjustment_id,
                'stock_before' => $stockBefore,
                'stock_after' => $stockBefore + $quantityChange,
                'user_id' => Auth::id(),
                'remarks' => $validated['reason'] . 
                    ($validated['adjustment_type'] === 'return_in' ? ' (Customer return - no stock change)' : 
                     ($validated['adjustment_type'] === 'damage_out' ? ' (Damaged - Stock deducted)' : 
                     ($validated['adjustment_type'] === 'customer_damaged' ? ' (Customer damaged return - no stock change)' : 
                     ($validated['adjustment_type'] === 'lost' ? ' (Lost/Missing recorded - no stock change)' : '')))),
            ]);
        });

        return redirect()->route('admin.adjustments.index')
            ->with('success', 'Stock adjustment recorded successfully.');
    }
}