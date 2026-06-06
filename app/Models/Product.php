<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'product_name',
        'sku',
        'category_id',
        'supplier_id',
        'description',
        'cost_price',
        'selling_price',
        'stock_quantity',
        'low_stock_threshold',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Check if product stock is low
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'product_id', 'product_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'product_id', 'product_id');
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class, 'product_id', 'product_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_id', 'product_id');
    }

    public function supplierReturns()
    {
        return $this->hasMany(SupplierReturn::class, 'product_id', 'product_id');
    }
}