<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FreezerInventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'category_id', 'processing_request_id', 'batch_number', 'product_name',
        'weight', 'cost_price', 'selling_price_per_kg', 'processing_date',
        'expiry_date', 'storage_location', 'temperature_zone', 'status', 'notes',
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
        'weight' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'selling_price_per_kg' => 'decimal:2',
        'processing_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function processingRequest()
    {
        return $this->belongsTo(ProcessingRequest::class);
    }

    public function scopeInStock($query)
    {
        return $query->where('status', 'in_stock');
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                     ->where('status', 'in_stock');
    }
}
