<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    protected $table = 'ward';
    protected $primaryKey = 'ward_id';
    
    protected $fillable = [
        'ward_name',
        'location',
        'total_beds',
        'available_beds',
        'ward_type',
        'floor'
    ];

    public function beds()
    {
        return $this->hasMany(Bed::class, 'ward_id', 'ward_id');
    }

    public function inPatients()
    {
        return $this->hasMany(InPatient::class, 'ward_id', 'ward_id');
    }
}