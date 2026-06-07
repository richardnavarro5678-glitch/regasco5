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
    // database only accepts: return_in, damage_out
    $dbAdjustmentType = match($originalType) {
        'customer_damaged' => 'return_in',
        'lost' => 'return_in',
        default => $originalType,
    };

    // Override for database storage
    $validated['adjustment_type'] = $dbAdjustmentType;

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

        // Create adjustment record
        $adjustment = StockAdjustment::create([
            'product_id' => $validated['product_id'],
            'adjustment_type' => $validated['adjustment_type'],
            'quantity' => $validated['quantity'],
            'reason' => $validated['reason'],
            'adjustment_date' => $validated['adjustment_date'],
            'user_id' => $validated['user_id'],
            'notes' => $validated['notes'] ?? null,
            'reference_id' => $validated['reference_id'] ?? null,
        ]);

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
            'remarks' => $validated['reason'] . ' (' . $originalType . ')',
        ]);

        // Update stock only if quantity changes
        if ($quantityChange !== 0) {
            $product->increment('stock_quantity', $quantityChange);
        }
    });

    return redirect()->route('admin.adjustments.index')
        ->with('success', 'Stock adjustment recorded successfully.');
}