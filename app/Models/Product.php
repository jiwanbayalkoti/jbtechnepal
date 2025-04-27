<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'name',
        'slug',
        'description',
        'image',
        'price',
        'discount_price',
        'brand',
        'model',
        'is_active'
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Get the subcategory that owns the product.
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Get the specifications for the product.
     */
    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function getPrimaryImageAttribute()
    {
        return $this->images()->where('is_primary', true)->first();
    }
    
    /**
     * Get the inventory for the product.
     */
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
    
    /**
     * Get the stock quantity for the product.
     */
    public function getStockAttribute()
    {
        return $this->inventory ? $this->inventory->quantity : 0;
    }
    
    /**
     * Check if the product is in stock.
     */
    public function getInStockAttribute()
    {
        return $this->stock > 0;
    }
    
    /**
     * Check if the product is low in stock.
     */
    public function getLowStockAttribute()
    {
        if (!$this->inventory) {
            return false;
        }
        
        return $this->inventory->isLow();
    }
}
