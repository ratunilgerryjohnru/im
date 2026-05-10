<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Ward extends Model
{
    use HasFactory;

    protected $primaryKey = 'ward_id';
    protected $table = 'ward';

    protected $fillable = [
        'ward_name',
        'location',
        'total_beds',
        'tel_extension',
        'floor',
        'ward_type'
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
     * Accessor: Calculate occupancy rate dynamically.
     */
    protected function occupancyRate(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->total_beds <= 0) {
                    return 0;
                }
                $occupied = $this->total_beds - ($this->available_beds ?? $this->total_beds);
                return round(($occupied / $this->total_beds) * 100, 2);
            },
        );
    }

    /**
     * Scope: Only show wards with available beds.
     */
    public function scopeHasSpace($query)
    {
        return $query->where('available_beds', '>', 0);
    }
}