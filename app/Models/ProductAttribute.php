<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    //
    protected $fillable = [
        'name', // name of the attribute (e.g., Color, Size)
        'type', // type of attribute (e.g., text, select, checkbox)
        'is_required', // whether the attribute is required for the product
        'is_filterable', // whether the attribute can be used for filtering products
        'sort_order', // order of display in product options
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'is_required' => 'boolean', // store as boolean
        'is_filterable' => 'boolean', // store as boolean
    ];

        // Relationships
    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    // Scopes
    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }
    
    
}
