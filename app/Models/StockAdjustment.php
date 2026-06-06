<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $primaryKey = 'adjustment_id';

    protected $fillable = [
        'product_id',
        'adjustment_type',
        'quantity',
        'reason',
        'reference_type',  // Dapat nandito
        'reference_id',    // Dapat nandito
        'user_id',
        'adjustment_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reference_id' => 'integer',
        'adjustment_date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}