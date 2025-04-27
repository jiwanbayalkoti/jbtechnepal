<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'link',
        'order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the page that owns the banner.
     */
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
    
    /**
     * Get all images for this banner.
     */
    public function images()
    {
        return $this->hasMany(BannerImage::class)->orderBy('display_order', 'asc');
    }
    
    /**
     * Get the primary image for this banner.
     */
    public function primaryImage()
    {
        return $this->hasOne(BannerImage::class)->where('is_primary', true);
    }
    
    /**
     * Get the image URL.
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        // First check if we have a primary image
        if ($this->primaryImage) {
            return $this->primaryImage->image_url;
        }
        
        // Then check if we have any images
        if ($this->images->isNotEmpty()) {
            return $this->images->first()->image_url;
        }
        
        // Fall back to the direct image_path if it exists
        if ($this->image_path) {
            return Storage::url($this->image_path);
        }
        
        return null;
    }
    
    /**
     * Get the banner's destination URL.
     *
     * @return string|null
     */
    public function getDestinationUrlAttribute()
    {
        if ($this->url) {
            return $this->url;
        }
        
        if ($this->page_id && $this->page) {
            return route('page.show', $this->page->slug);
        }
        
        return null;
    }
    
    /**
     * Get status badge HTML.
     *
     * @return string
     */
    public function getStatusBadgeAttribute()
    {
        if ($this->is_active) {
            return '<span class="badge bg-success">Active</span>';
        }
        
        return '<span class="badge bg-danger">Inactive</span>';
    }
}
