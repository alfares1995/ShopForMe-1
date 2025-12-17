<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderShipping extends Model
{
    //
    protected $fillable = [
        'order_id',
        'shipping_method_id',
        'carrier',
        'tracking_number',
        'tracking_url',
        'shipped_at',
        'estimated_delivery',
        'created_at',
        'updated_at',
    ];
    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }
}
