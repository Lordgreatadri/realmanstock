<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'uuid', 'name', 'email', 'phone', 'address', 'city', 'state',
        'preferred_delivery', 'preferred_processing', 'allow_credit',
        'credit_limit', 'outstanding_balance', 'notes',
    ];

    protected $casts = [
        'allow_credit' => 'boolean',
        'credit_limit' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->uuid)) {
                $customer->uuid = Str::uuid()->toString();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function processingRequests()
    {
        return $this->hasMany(ProcessingRequest::class);
    }

    public function updateBalance($amount)
    {
        $this->outstanding_balance += $amount;
        $this->save();
    }
}
