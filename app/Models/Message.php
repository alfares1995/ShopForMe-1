<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
    protected $fillable = ['user_id', 'content'];
    protected $casts = [
        'user_id' => 'integer',
        'content' => 'string',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Additional methods or relationships can be defined here
    // For example, if you want to add a method to get the formatted content
    // public function getFormattedContentAttribute()
    // {
    //     return nl2br(e($this->content));
    // }
    // You can also add scopes, accessors, or mutators as needed
    // For example, if you want to add a scope to filter messages by user
    // public function scopeByUser($query, $userId)
}
