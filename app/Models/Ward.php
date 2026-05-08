<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * Updated to match the singular 'ward' table in Supabase.
     */
    protected $table = 'ward';

    /**
     * The primary key associated with the table.
     * Updated to 'ward_id' as seen in your Supabase schema.
     */
    protected $primaryKey = 'ward_id';

    /**
     * The attributes that are mass assignable.
     * All columns from your Supabase screenshot are included here.
     */
    protected $fillable = [
        'ward_name',
        'ward_type',    // Matches the 'ward_type' column in Supabase
        'location',     // Matches the 'location' column in Supabase
        'total_beds',
        'available_beds',
        'dept_id',      // Matches the 'dept_id' column in Supabase
        'ward_phone',   // New column from your recent screenshot
        'notes'         // New column from your recent screenshot
    ];

    /**
     * Helper to calculate occupancy percentage
     */
    public function getOccupancyRateAttribute()
    {
        if ($this->total_beds <= 0) return 0;
        
        $occupied = $this->total_beds - $this->available_beds;
        return ($occupied / $this->total_beds) * 100;
    }
}