<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReturnRequest extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'returns';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'customer_id',
        'return_number',
        'status',
        'reason',
        'refund_method',
        'refund_amount',
        'return_tracking_number',
        'admin_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'processed_at' => 'datetime',
    ];

    /**
     * Get the order associated with the return.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the customer associated with the return.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the items for the return.
     */
    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    /**
     * Get badge class for status
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'requested' => 'bg-warning',
            'approved' => 'bg-info',
            'received' => 'bg-primary',
            'processed' => 'bg-success',
            'completed' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get formatted created date
     */
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->created_at)->format('M d, Y');
    }

    /**
     * Get formatted processed date
     */
    public function getFormattedProcessedDateAttribute()
    {
        return $this->processed_at ? Carbon::parse($this->processed_at)->format('M d, Y') : null;
    }
}
