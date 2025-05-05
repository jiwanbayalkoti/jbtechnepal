<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'is_featured'
    ];

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the specification types for the category.
     */
    public function specificationTypes(): HasMany
    {
        return $this->hasMany(SpecificationType::class);
    }

    /**
     * Get the subcategories for the category.
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }
}
