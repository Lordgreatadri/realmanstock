<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'order_number', 'customer_id', 'user_id', 'status', 'subtotal',
        'processing_fee', 'delivery_fee', 'discount', 'tax', 'total',
        'amount_paid', 'balance', 'payment_method', 'delivery_type',
        'delivery_address', 'delivery_date', 'special_instructions', 'notes',
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = Str::uuid()->toString();
            }
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid());
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * Update order status and send notifications
     * 
     * @param string $newStatus One of: pending, processing, payment_received, ready_for_delivery, out_for_delivery, delivered, cancelled
     * @param string|null $notes Optional notes about the status change
     * @param bool $notify Whether to send notifications (default: true)
     * @return void
     */
    public function updateStatus($newStatus, $notes = null, $notify = true)
    {
        // Validate status
        $validStatuses = ['pending', 'processing', 'payment_received', 'ready_for_delivery', 'out_for_delivery', 'delivered', 'cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid order status: {$newStatus}. Must be one of: " . implode(', ', $validStatuses));
        }

        $oldStatus = $this->status;
        
        // Don't update if status hasn't changed
        if ($oldStatus === $newStatus) {
            return;
        }

        $this->status = $newStatus;
        $this->save();

        // Record status history
        $this->statusHistories()->create([
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'notes' => $notes,
            'user_id' => auth()->id(),
        ]);

        // Send notification to customer
        if ($notify && $this->customer) {
            $this->customer->notify(new \App\Notifications\OrderStatusChanged($this, $oldStatus));
        }
    }

    /**
     * Check if order status is one of the specified values
     * 
     * @param string|array $statuses
     * @return bool
     */
    public function hasStatus($statuses): bool
    {
        if (is_string($statuses)) {
            $statuses = [$statuses];
        }
        
        return in_array($this->status, $statuses);
    }

    /**
     * Get human-readable status name
     * 
     * @return string
     */
    public function getStatusNameAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Scope for filtering orders by status
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $statuses
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, $statuses)
    {
        if (is_string($statuses)) {
            $statuses = [$statuses];
        }
        
        return $query->whereIn('status', $statuses);
    }

    public function recordPayment($amount, $method)
    {
        $this->amount_paid += $amount;
        $this->balance = $this->total - $this->amount_paid;
        $this->payment_method = $method;
        $this->save();
    }
}
