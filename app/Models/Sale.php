<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'sale_id';

    protected $fillable = [
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
        'user_id',
        'sale_date',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'sale_date' => 'datetime',
    ];

    public function product()
    {
        // FIX: withTrashed() to load even soft-deleted products
        return $this->belongsTo(Product::class, 'product_id', 'product_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'reference_id', 'sale_id')
            ->where('reference_type', 'sale');
    }

    // FIX: Helper method to check if product is actually deleted (trashed)
    public function isProductDeleted(): bool
    {
        return $this->product && $this->product->trashed();
    }

    // FIX: Helper method to get product display name
    public function getProductName(): string
    {
        if ($this->isProductDeleted()) {
            return 'N/A';
        }
        return $this->product->product_name ?? 'N/A';
    }
}