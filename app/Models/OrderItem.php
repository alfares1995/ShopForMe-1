<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    //
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'quantity',
        'price',
        'total',
        'product_options',  
    ];
    protected $casts = [
        'product_options' => 'array', // Assuming product_options is stored as a JSON array
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'quantity' => 'integer',
        'product_id' => 'integer',
        'order_id' => 'integer',
        'product_name' => 'string',
        'product_sku' => 'string',    ];
  
   // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    


}
