<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bed extends Model
{
    use HasFactory;

    // Matches your Supabase table name 'bed' and primary key 'bed_id'
    protected $table = 'bed';
    protected $primaryKey = 'bed_id';

    protected $fillable = [
        'bed_name',
        'ward_id',
        'bed_type',
        'is_available',
        'maintenance_status',
        'last_cleaned' // Matches Supabase schema
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'last_cleaned' => 'datetime', // Ensures timestamps are Carbon objects
        ];
    }

    /**
     * Appends custom attributes to the model's JSON form.
     * 'is_inconsistent' helps the frontend detect if a bed is occupied without a patient.
     */
    protected $appends = ['is_inconsistent'];

    /**
     * Relationship: A bed belongs to a specific ward.
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'ward_id');
    }

    /**
     * Relationship: The current active inpatient assigned to this bed.
     * FIX: Specifying 'inpatient_id' inside latestOfMany avoids the "column id does not exist" error.
     */
    public function currentInpatient(): HasOne
    {
        return $this->hasOne(Inpatient::class, 'bed_id', 'bed_id')
            ->whereNull('discharge_date')
            ->latestOfMany('inpatient_id');
    }

    /**
     * Scope: Filter only beds that are actually ready for a patient.
     */
    public function scopeReadyForPatient($query)
    {
        return $query->where('is_available', true)
            ->where(function ($q) {
                $q->whereNull('maintenance_status')
                    ->orWhere('maintenance_status', 'operational');
            });
    }

    /**
     * Accessor: Detects if the database state doesn't match the actual occupancy.
     * Returns true if the bed is marked 'Occupied' (is_available = false) 
     * but no active inpatient record exists.
     */
    public function getIsInconsistentAttribute(): bool
    {
        // Using the loaded relationship directly for better performance during serialization
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