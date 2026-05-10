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
        'bed_number',  // Changed from bed_name to bed_number
        'ward_id',
        'bed_type',
        'is_available',
        'maintenance_status',
        'is_active'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['is_inconsistent', 'bed_name'];

    /**
     * Accessor for bed_name (for backward compatibility)
     */
    public function getBedNameAttribute()
    {
        return $this->bed_number;
    }

    /**
     * Relationship: A bed belongs to a specific ward.
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'ward_id');
    }

    /**
     * Relationship: The current active inpatient assigned to this bed.
     */
    public function currentInpatient(): HasOne
    {
        return $this->hasOne(InPatient::class, 'bed_id', 'bed_id')
            ->whereNull('actual_leave')
            ->latestOfMany('inpatient_id');
    }

    /**
     * Scope: Filter only beds that are actually ready for a patient.
     */
    public function scopeReadyForPatient($query)
    {
        return $query->where('is_available', true)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('maintenance_status')
                    ->orWhere('maintenance_status', 'operational');
            });
    }

    /**
     * Accessor: Detects if the database state doesn't match the actual occupancy.
     */
    public function getIsInconsistentAttribute(): bool
    {
        return !$this->is_available && !$this->currentInpatient;
    }

    /**
     * Helper: Check if the bed is truly occupied by a patient record.
     */
    public function isOccupied(): bool
    {
        return (bool) $this->currentInpatient;
    }

    /**
     * Scope: Filter only operational beds (not under maintenance)
     */
    public function scopeOperational($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('maintenance_status')
                ->orWhere('maintenance_status', '!=', 'under_maintenance');
        });
    }
}