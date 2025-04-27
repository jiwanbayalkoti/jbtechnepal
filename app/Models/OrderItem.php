<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'price',
        'subtotal',
        'options',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product associated with the item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the return items for this order item.
     */
    public function returns()
    {
        return $this->hasMany(ReturnItem::class, 'order_item_id');
    }

    /**
     * Get the total quantity returned for this item.
     */
    public function returnedQuantity()
    {
        return $this->returns()->sum('quantity');
    }

    /**
     * Check if this item can be returned (if there are any quantities not yet returned).
     */
    public function canReturn()
    {
        return $this->returnedQuantity() < $this->quantity;
    }

    /**
     * Get the available quantity that can still be returned.
     */
    public function availableToReturn()
    {
        return $this->quantity - $this->returnedQuantity();
    }
} 