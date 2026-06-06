<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';
    
    protected $fillable = [
        'username',
        'name',
        'password',
        'role',
        'phone_number',
        'is_active',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'password_changed_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCashier()
    {
        return $this->role === 'cashier';
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'created_by');
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class, 'user_id');
    }
}