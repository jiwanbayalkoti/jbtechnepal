<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Advertisement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'url',
        'content',
        'image',
        'position',
        'display_order',
        'start_date',
        'end_date',
        'is_active',
        'views',
        'clicks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'views' => 'integer',
        'clicks' => 'integer',
    ];

    /**
     * Get the image URL.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        
        return asset('images/no-image.png');
    }

    /**
     * Check if the advertisement is currently active.
     *
     * @return bool
     */
    public function isCurrentlyActive()
    {
        $now = now();
        
        // Check if the ad is marked as active
        if (!$this->is_active) {
            return false;
        }
        
        // Check if the current date is within the start and end dates
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }
        
        return true;
    }

    /**
     * Record a view of this advertisement.
     *
     * @return void
     */
    public function recordView()
    {
        $this->increment('views');
    }

    /**
     * Record a click on this advertisement.
     *
     * @return void
     */
    public function recordClick()
    {
        $this->increment('clicks');
    }

    /**
     * Get the status badge HTML.
     *
     * @return string
     */
    public function getStatusBadgeAttribute()
    {
        if ($this->isCurrentlyActive()) {
            return '<span class="badge bg-success">Active</span>';
        }
        
        if (!$this->is_active) {
            return '<span class="badge bg-danger">Inactive</span>';
        }
        
        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return '<span class="badge bg-warning">Scheduled</span>';
        }
        
        if ($this->end_date && $now->gt($this->end_date)) {
            return '<span class="badge bg-secondary">Expired</span>';
        }
        
        return '<span class="badge bg-info">Unknown</span>';
    }

    /**
     * Get formatted position name.
     *
     * @return string
     */
    public function getPositionNameAttribute()
    {
        $positions = [
            'homepage_slider' => 'Homepage Slider',
            'sidebar' => 'Sidebar',
            'category_page' => 'Category Page',
            'product_page' => 'Product Page',
            'footer' => 'Footer'
        ];
        
        return $positions[$this->position] ?? ucfirst(str_replace('_', ' ', $this->position));
    }

    /**
     * Scope a query to only include active advertisements.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        $today = Carbon::today()->format('Y-m-d');
        
        return $query->where('is_active', true)
            ->where(function($q) use ($today) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $today);
            })
            ->where(function($q) use ($today) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $today);
            });
    }

    /**
     * Scope a query to only include advertisements for a specific position.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $position
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Get the status of the advertisement.
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'inactive';
        }
        
        $today = Carbon::today();
        
        if ($this->start_date && $this->start_date->isAfter($today)) {
            return 'upcoming';
        }
        
        if ($this->end_date && $this->end_date->isBefore($today)) {
            return 'expired';
        }
        
        return 'active';
    }

    /**
     * Get the click-through rate.
     *
     * @return float
     */
    public function getCtrAttribute()
    {
        if ($this->views === 0) {
            return 0;
        }
        
        return round(($this->clicks / $this->views) * 100, 2);
    }

    /**
     * Get the days remaining until expiration.
     *
     * @return int|null
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->end_date) {
            return null;
        }
        
        $today = Carbon::today();
        
        if ($this->end_date->isPast()) {
            return 0;
        }
        
        return $today->diffInDays($this->end_date);
    }
}
