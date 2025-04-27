<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_id',
        'type',
        'quantity',
        'reason',
        'reference',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Get the inventory that owns the adjustment.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Get the type label attribute.
     *
     * @return string
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'add' => 'Added',
            'remove' => 'Removed',
            default => ucfirst($this->type),
        };
    }

    /**
     * Get the type badge class attribute.
     *
     * @return string
     */
    public function getTypeBadgeAttribute()
    {
        return match($this->type) {
            'add' => 'bg-success',
            'remove' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get the reason label attribute.
     *
     * @return string
     */
    public function getReasonLabelAttribute()
    {
        return match($this->reason) {
            'purchase' => 'Purchase',
            'sale' => 'Sale',
            'return' => 'Return',
            'damaged' => 'Damaged/Defective',
            'correction' => 'Inventory Correction',
            'initial' => 'Initial Setup',
            default => ucfirst($this->reason),
        };
    }
} 