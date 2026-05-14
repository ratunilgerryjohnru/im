<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'is_active',
        'maintenance_status'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'ward_id');
    }

    public function inPatients()
    {
        return $this->hasMany(InPatient::class, 'bed_id', 'bed_id');
    }
}