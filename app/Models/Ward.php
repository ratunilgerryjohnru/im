<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends Model
{
    use HasFactory;

    protected $primaryKey = 'ward_id';
    protected $table = 'ward';

    protected $fillable = [
        'ward_name',
        'location',
        'total_beds',
        'available_beds',  // This column exists in schema!
        'tel_extension',
        'floor',
        'ward_type'
    ];

    protected $casts = [
        'total_beds' => 'integer',
        'available_beds' => 'integer',
    ];

    /**
     * Relationship: A ward has many beds.
     */
    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class, 'ward_id', 'ward_id');
    }

    /**
     * Relationship: A ward has many inpatients.
     */
    public function inpatients(): HasMany
    {
        return $this->hasMany(InPatient::class, 'ward_id', 'ward_id');
    }

    /**
     * Get the count of currently occupied beds
     */
    public function getOccupiedBedsCountAttribute(): int
    {
        return $this->total_beds - ($this->available_beds ?? 0);
    }

    /**
     * Get occupancy rate
     */
    public function getOccupancyRateAttribute(): float
    {
        if ($this->total_beds <= 0) {
            return 0;
        }
        return round(($this->occupied_beds_count / $this->total_beds) * 100, 2);
    }

    /**
     * Scope: Only show wards with available beds.
     */
    public function scopeHasSpace($query)
    {
        return $query->where('available_beds', '>', 0);
    }

    /**
     * Refresh available beds count from actual bed records
     */
    public function refreshAvailableBedsCount(): void
    {
        $availableCount = $this->beds()->where('is_available', true)->count();
        $this->update(['available_beds' => $availableCount]);
    }
}