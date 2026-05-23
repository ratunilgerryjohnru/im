<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bed extends Model
{
    use HasFactory;

    protected $table = 'bed';
    protected $primaryKey = 'bed_id';

    protected $fillable = [
        'bed_number',
        'ward_id',
        'bed_type',
        'is_available',
        'maintenance_status',
        'is_active'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'bed_id' => 'integer',
        'ward_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['bed_name'];

    public function getBedNameAttribute()
    {
        return $this->bed_number;
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'ward_id');
    }

    public function currentInpatient(): HasOne
    {
        return $this->hasOne(InPatient::class, 'bed_id', 'bed_id')
            ->whereNull('actual_leave')
            ->latestOfMany('inpatient_id');
    }

    public function inpatients(): HasMany
    {
        return $this->hasMany(InPatient::class, 'bed_id', 'bed_id');
    }

    public function scopeReadyForPatient($query)
    {
        return $query->where('is_available', true)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('maintenance_status')
                    ->orWhere('maintenance_status', 'operational');
            });
    }

    public function isOccupied(): bool
    {
        return (bool) $this->currentInpatient;
    }

    public function scopeOperational($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('maintenance_status')
                ->orWhere('maintenance_status', '!=', 'under_maintenance');
        });
    }
}