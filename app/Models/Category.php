<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
   //

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        
        'meta_title',
        'meta_description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRootCategories($query)
    {
        return $query->whereNull('parent_id');
    }
    public function scopeWithChildren($query)
    {
        return $query->with('children');
    }
    public function scopeWithParent($query)
    {
        return $query->with('parent');
    }
    public function scopeWithParentAndChildren($query)
    {
        return $query->with(['parent', 'children']);
    }
    public static function generateSlugSuffix()
    {
        return is_null(self::max('id')) ? 1 : self::max('id') + 1;
    }

   
 

}
