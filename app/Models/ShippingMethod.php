<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'price', // base cost of shipping
        'free_shipping_threshold', // minimum order amount for free shipping
        'estimated_days_min', // minimum estimated delivery days
        'estimated_days_max', // maximum estimated delivery days
        'sort_order', // order of display in shipping options
        'is_active', // whether the shipping method is active
        'created_at',
        'updated_at',
    ];
     // Relationships
    public function orderShippings()
    {
        return $this->hasMany(OrderShipping::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
