<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;

class Model extends EloquentModel
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'brand_id',
        'category_id',
        'subcategory_id',
        'is_active',
        'features',
        'specifications'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
        'specifications' => 'array',
    ];
    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate slug from name when creating or updating
        static::saving(function ($model) {
            if ($model->name && (!$model->slug || $model->isDirty('name'))) {
                $model->slug = static::generateUniqueSlug($model->name);
            }
        });
    }
    
    /**
     * Generate a unique slug.
     *
     * @param string $name
     * @return string
     */
    public static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
        
        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Get the brand that owns the model.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the category that owns the model.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the subcategory that owns the model.
     */
    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
} 