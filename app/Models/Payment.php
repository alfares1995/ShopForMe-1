<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_id',
        'amount',
        'transaction_id',
        'status',
        'payment_date',
        'currency',
        'payment_details',
        'paid_at',
        'failure_reason',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
        'payment_date' => 'datetime',
        'paid_at' => 'datetime',
        'payment_details' => 'array', // Assuming payment_details is stored as a JSON array
        'currency' => 'string',
    ];
  
    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    
}
