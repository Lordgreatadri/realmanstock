<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'category_id', 'tag_number', 'breed', 'gender', 'date_of_birth',
        'purchase_price', 'purchase_date', 'supplier', 'current_weight',
        'status', 'health_notes', 'image', 'is_vaccinated',
        'last_vaccination_date', 'selling_price_per_kg', 'fixed_selling_price', 'notes',
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
        'date_of_birth' => 'date',
        'purchase_date' => 'date',
        'last_vaccination_date' => 'date',
        'purchase_price' => 'decimal:2',
        'current_weight' => 'decimal:2',
        'selling_price_per_kg' => 'decimal:2',
        'fixed_selling_price' => 'decimal:2',
        'is_vaccinated' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function healthRecords()
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function weightRecords()
    {
        return $this->hasMany(WeightRecord::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function getSellingPriceAttribute()
    {
        if ($this->fixed_selling_price) {
            return $this->fixed_selling_price;
        }
        if ($this->selling_price_per_kg && $this->current_weight) {
            return $this->selling_price_per_kg * $this->current_weight;
        }
        return null;
    }
}
