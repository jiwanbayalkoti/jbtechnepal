<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'sku',
        'quantity',
        'reorder_level',
        'status',
        'location',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'reorder_level' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'stock_status',
    ];

    /**
     * Get the product that owns the inventory.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the adjustments for the inventory.
     */
    public function adjustments()
    {
        return $this->hasMany(InventoryAdjustment::class);
    }

    /**
     * Get the stock status attribute.
     *
     * @return string
     */
    public function getStockStatusAttribute()
    {
        if ($this->quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->quantity <= $this->reorder_level) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Get the stock status label attribute.
     *
     * @return string
     */
    public function getStockStatusLabelAttribute()
    {
        return match($this->stock_status) {
            'out_of_stock' => 'Out of Stock',
            'low_stock' => 'Low Stock',
            'in_stock' => 'In Stock',
            default => 'Unknown',
        };
    }

    /**
     * Get the stock status badge class attribute.
     *
     * @return string
     */
    public function getStockStatusBadgeAttribute()
    {
        return match($this->stock_status) {
            'out_of_stock' => 'bg-danger',
            'low_stock' => 'bg-warning',
            'in_stock' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    /**
     * Get the transactions for the inventory.
     */
    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Check if inventory is low.
     * 
     * @return bool
     */
    public function isLow()
    {
        return $this->quantity <= $this->reorder_level;
    }

    /**
     * Adjust the inventory quantity.
     * 
     * @param int $quantity
     * @param string $type
     * @param string|null $reason
     * @param int|null $userId
     * @param string|null $referenceNumber
     * @return InventoryTransaction
     */
    public function adjustStock($quantity, $type, $reason = null, $userId = null, $referenceNumber = null)
    {
        $quantityBefore = $this->quantity;
        
        // Calculate new quantity based on transaction type
        if ($type === 'stock_in' || $type === 'return') {
            $this->quantity += $quantity;
        } elseif ($type === 'stock_out' || $type === 'transfer') {
            $this->quantity -= $quantity;
        } else { // adjustment
            $this->quantity = $quantity;
            $quantity = $quantity - $quantityBefore;
        }
        
        $quantityAfter = $this->quantity;
        $this->save();
        
        // Create transaction record
        return $this->transactions()->create([
            'type' => $type,
            'quantity' => abs($quantity),
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'reason' => $reason,
            'user_id' => $userId,
            'reference_number' => $referenceNumber,
        ]);
    }
}
