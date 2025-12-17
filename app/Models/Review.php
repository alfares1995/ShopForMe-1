<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    //
    protected $fillable = [
        'user_id',
        'product_id',
        'order_id', // optional, if review is linked to a specific order
        'reviewer_name', // name of the reviewer
        'reviewer_email', // email of the reviewer
        'rating', // 1-5 scale
        'title',
        'comment', // review text
        'is_verified', // whether the review is from a verified purchase
        'is_approved', // whether the review is approved for display
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'rating' => 'integer', // ensure rating is stored as integer
        'is_verified' => 'boolean', // store as boolean
        'is_approved' => 'boolean', // store as boolean
    ];
     // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
