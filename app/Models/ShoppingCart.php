<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    //
    protected $fillable = [
        'user_id',
        'session_id', // for guest users
        'product_id',
        'quantity',
        'price', // store price at time of adding to cart
        'product_options', // for product variants/options
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'product_options' => 'array', // store as JSON
        'price' => 'decimal:2', // ensure price is stored as decimal
    ];
   // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getTotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
    public function scopeActive($query)
    {
        return $query->where('quantity', '>', 0);
    }


}
