<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ward_name',
        'category',
        'total_beds',
        'available_beds',
    ];

    /**
     * Optional: Helper to calculate occupancy percentage
     */
    public function getOccupancyRateAttribute()
    {
        if ($this->total_beds <= 0) return 0;
        
        $occupied = $this->total_beds - $this->available_beds;
        return ($occupied / $this->total_beds) * 100;
    }
}