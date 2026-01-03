<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessingRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'order_id', 'animal_id', 'customer_id', 'processed_by', 'status',
        'processing_fee', 'requested_date', 'scheduled_date', 'completed_date',
        'live_weight', 'dressed_weight', 'special_instructions', 'quality_notes',
    ];

    /**
     * Boot function for using with UUID
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $casts = [
        'processing_fee' => 'decimal:2',
        'live_weight' => 'decimal:2',
        'dressed_weight' => 'decimal:2',
        'requested_date' => 'date',
        'scheduled_date' => 'date',
        'completed_date' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
