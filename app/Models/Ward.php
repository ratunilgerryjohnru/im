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

    /**
     * Supabase uses ward_id as the Primary Key.
     */
    protected $primaryKey = 'ward_id';

    /**
     * The table associated with the model.
     * (Optional: Laravel assumes 'wards', but your Supabase table is 'ward')
     */
    protected $table = 'ward';

    protected $fillable = [
        'ward_name', 
        'ward_type', 
        'location', 
        'total_beds', 
        'available_beds', 
        'dept_id',
         'floor' 
    ];

    /**
     * Relationship: A ward belongs to a department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'dept_id', 'dept_id');
    }

    /**
     * Relationship: A ward has many beds.
     * This is required for the Bed Management grid in show.blade.php.
     */
    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class, 'ward_id', 'ward_id');
    }

    /**
     * Accessor: Calculate occupancy rate dynamically.
     * Usage in Blade: {{ $ward->occupancy_rate }}%
     */
    protected function occupancyRate(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->total_beds <= 0) {
                    return 0;
                }
                $occupied = $this->total_beds - $this->available_beds;
                return round(($occupied / $this->total_beds) * 100, 2);
            },
        );
    }

    /**
     * Scope: Only show wards with available beds.
     * Usage: Ward::hasSpace()->get();
     */
    public function scopeHasSpace($query)
    {
        return $query->where('available_beds', '>', 0);
    }
} // <--- This closing brace must be at the very end of the file.