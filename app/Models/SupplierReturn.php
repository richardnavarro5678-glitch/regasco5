<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'return_id';

    protected $fillable = [
        'product_id',
        'supplier_id',
        'user_id',
        'quantity',
        'reason',
        'return_date',  // Dapat nandito ito
        'notes',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'return_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}