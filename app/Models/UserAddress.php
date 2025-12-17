<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    //
    protected $fillable = [
        'user_id',
        'type', // shipping, billing
        'first_name',
        'last_name',
        'company',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
    ];
    protected $casts = [
        'is_default' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
