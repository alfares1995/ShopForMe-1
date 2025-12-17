<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    //
    protected $fillable = [
        'name', // name of the product
        'slug', // unique slug for the product
        'description', // detailed description of the product
        'short_description', // short description of the product
        'sku', // stock keeping unit
        'price', // price of the product
        'compare_price', // price to compare against, for discounts
        'cost_price', // cost price of the product
        'stock_quantity', // quantity in stock
        'min_stock_level', // minimum stock level before alert
        'track_stock', // whether to track stock levels
        'stock_status', // status of the stock (in_stock, out_of_stock, on backorder)
        'weight', // weight of the product
        'dimensions', // dimensions of the product
        'brand_id', // foreign key to the brand model
        'meta_title', // SEO title for the product
        'meta_description', // SEO description for the product
        'is_active', // whether the product is active or not
        'is_featured', // whether the product is featured
        'is_digital', // whether the product is digital
        'sort_order', // order in which the product appears in listings
        'created_at',
        'updated_at',
    ];

       protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'track_stock' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_digital' => 'boolean',
    ];
    // Relationships
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function cartItems()
    {
        return $this->hasMany(ShoppingCart::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock');
    }

    // Accessors
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }


}
