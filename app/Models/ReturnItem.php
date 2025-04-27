<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'return_id',
        'order_item_id',
        'quantity',
        'condition',
        'reason',
        'approved',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'approved' => 'boolean',
    ];

    /**
     * Get the return request that owns the item.
     */
    public function return()
    {
        return $this->belongsTo(\App\Models\ProductReturn::class, 'return_id');
    }

    /**
     * Get the order item associated with the return item.
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    /**
     * Get badge class for condition
     */
    public function getConditionBadgeAttribute()
    {
        return match($this->condition) {
            'new' => 'bg-success',
            'used' => 'bg-info',
            'damaged' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
} 