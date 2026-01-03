<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'animal_id', 'user_id', 'type', 'record_date', 'treatment_name',
        'symptoms', 'diagnosis', 'prescription', 'cost',
        'next_checkup_date', 'notes',
    ];

    protected $casts = [
        'record_date' => 'date',
        'next_checkup_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
