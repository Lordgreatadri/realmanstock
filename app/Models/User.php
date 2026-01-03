<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'phone',
        'company_name',
        'purpose',
        'is_approved',
        'approved_at',
        'approved_by',
        'phone_verified',
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_approved' => 'boolean',
        'phone_verified' => 'boolean',
        'approved_at' => 'datetime',
        'otp_expires_at' => 'datetime',
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvedUsers()
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    // Methods
    public function generateOTP()
    {
        $this->otp = rand(100000, 999999);
        $this->otp_expires_at = now()->addMinutes(config('services.sms.otp_expiry_minutes', 10));
        $this->save();

        return $this->otp;
    }

    public function verifyOTP($otp)
    {
        if ($this->otp === $otp && $this->otp_expires_at > now()) {
            $this->phone_verified = true;
            $this->otp = null;
            $this->otp_expires_at = null;
            $this->save();
            return true;
        }
        return false;
    }

    public function approve($approverId)
    {
        $this->is_approved = true;
        $this->approved_at = now();
        $this->approved_by = $approverId;
        $this->save();
    }
}
