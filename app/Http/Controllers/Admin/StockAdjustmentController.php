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
        
        // FIX: Count by original type using reason field or separate query
        // Since we map customer_damaged and lost to return_in in DB,
        // we need to count them differently
        
        // Count return_in (original)
        $typeStats['return_in'] = [
            'count' => StockAdjustment::where('adjustment_type', 'return_in')
                ->where('reason', 'not like', '(%')
                ->count(),
            'total_quantity' => StockAdjustment::where('adjustment_type', 'return_in')
                ->where('reason', 'not like', '(%')
                ->sum('quantity'),
            'last_adjustment' => StockAdjustment::where('adjustment_type', 'return_in')
                ->where('reason', 'not like', '(%')
                ->latest()
                ->first(),
        ];
        
        // Count damage_out
        $typeStats['damage_out'] = [
            'count' => StockAdjustment::where('adjustment_type', 'damage_out')->count(),
            'total_quantity' => StockAdjustment::where('adjustment_type', 'damage_out')->sum('quantity'),
            'last_adjustment' => StockAdjustment::where('adjustment_type', 'damage_out')->latest()->first(),
        ];
        
        // FIX: Count customer_damaged by checking reason field for marker
        $typeStats['customer_damaged'] = [
            'count' => StockAdjustment::where('adjustment_type', 'return_in')
                ->where('reason', 'like', '%(customer_damaged)%')
                ->count(),
            'total_quantity' => StockAdjustment::where('adjustment_type', 'return_in')
                ->where('reason', 'like', '%(customer_damaged)%')
                ->sum('quantity'),
            'last_adjustment' => StockAdjustment::where('adjustment_type', 'return_in')
                ->where('reason', 'like', '%(customer_damaged)%')
                ->latest()
                ->first(),
        ];
        
        // FIX: Count lost by checking reason field for marker
        $typeStats['lost'] = [
            'count' => StockAdjustment::where('adjustment_type', 'return_in')
                ->where('reason', 'like', '%(lost)%')
                ->count(),
            'total_quantity' => StockAdjustment::where('adjustment_type', 'return_in')
                ->where('reason', 'like', '%(lost)%')
                ->sum('quantity'),
            'last_adjustment' => StockAdjustment::where('adjustment_type', 'return_in')
                ->where('reason', 'like', '%(lost)%')
                ->latest()
                ->first(),
        ];

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

        // FIX: Query based on mapped types
        $query = StockAdjustment::with(['product', 'user']);
        
        if ($type === 'customer_damaged') {
            $query->where('adjustment_type', 'return_in')
                  ->where('reason', 'like', '%(customer_damaged)%');
        } elseif ($type === 'lost') {
            $query->where('adjustment_type', 'return_in')
                  ->where('reason', 'like', '%(lost)%');
        } elseif ($type === 'return_in') {
            $query->where('adjustment_type', 'return_in')
                  ->where('reason', 'not like', '%(customer_damaged)%')
                  ->where('reason', 'not like', '%(lost)%');
        } else {
            $query->where('adjustment_type', $type);
        }

        $adjustments = $query->latest()->paginate(20);

        $stats = [
            'total_count' => $query->count(),
            'total_quantity' => $query->sum('quantity'),
            'this_month' => (clone $query)->whereMonth('adjustment_date', now()->month)
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
            'adjustment_type' => 'required|in:return_in,damage_out,customer_damaged,lost',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'reference_id' => 'nullable|integer',
            'adjustment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        // Store original type for logic
        $originalType = $validated['adjustment_type'];

        // FIX: Map frontend adjustment_type to valid database values for stock_adjustments
        $dbAdjustmentType = match($originalType) {
            'customer_damaged' => 'return_in',
            'lost' => 'return_in',
            default => $originalType,
        };

        // Override for database storage
        $validated['adjustment_type'] = $dbAdjustmentType;

        // FIX: Add marker to reason for identification
        $validated['reason'] = $validated['reason'] . ' (' . $originalType . ')';

        DB::transaction(function () use ($validated, $originalType) {
            $product = Product::find($validated['product_id']);
            $stockBefore = $product->stock_quantity;

            $quantityChange = match($originalType) {
                'return_in' => 0,
                'damage_out' => -$validated['quantity'],
                'customer_damaged' => 0,
                'lost' => 0,
                default => -$validated['quantity'],
            };

            if ($quantityChange < 0 && $stockBefore < abs($quantityChange)) {
                throw new \Exception('Insufficient stock for this adjustment.');
            }

            $adjustment = StockAdjustment::create($validated);

            // Update stock only if quantity changes
            if ($quantityChange !== 0) {
                $product->increment('stock_quantity', $quantityChange);
            }

            // FIX: Always create stock movement for record keeping (even if quantity is 0)
            StockMovement::create([
                'product_id' => $validated['product_id'],
                'movement_type' => 'adjustment',
                'quantity' => abs($quantityChange),
                'reference_type' => 'adjustment',
                'reference_id' => $adjustment->adjustment_id,
                'stock_before' => $stockBefore,
                'stock_after' => $stockBefore + $quantityChange,
                'user_id' => Auth::id(),
                'remarks' => $validated['reason'],
            ]);
        });

        return redirect()->route('admin.adjustments.index')
            ->with('success', 'Stock adjustment recorded successfully.');
    }
}