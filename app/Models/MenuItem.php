<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'url',
        'route_name',
        'icon',
        'location',
        'active',
        'order',
        'parent_id',
        'category_id',
        'slug',
        'content',
        'is_dynamic_page',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'order' => 'integer',
        'is_dynamic_page' => 'boolean',
    ];

    /**
     * Get the parent menu item.
     */
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * Get the child menu items.
     */
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }
    
    /**
     * Scope a query to only include active menu items.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    /**
     * Scope a query to include menu items by location.
     */
    public function scopeLocation($query, $location)
    {
        return $query->where('location', $location);
    }
    
    /**
     * Get menu items for a specific location.
     */
    public static function getMenuItems($location = 'main')
    {
        return self::active()
            ->location($location)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->with(['children' => function($query) {
                $query->active()->orderBy('order');
            }])
            ->get();
    }

    /**
     * Get the related category if this menu item represents a category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the dynamic page URL if this menu item is a dynamic page
     */
    public function getDynamicPageUrlAttribute()
    {
        if ($this->is_dynamic_page && $this->slug) {
            return route('dynamic.page', $this->slug);
        }
        
        return null;
    }
}
