<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InPatient extends Model
{
    use HasFactory;

    protected $table = 'in_patient';
    protected $primaryKey = 'inpatient_id';
    
    protected $fillable = [
        'patient_id',
        'ward_id',
        'bed_id',
        'date_admitted',
        'expected_leave',
        'actual_leave',
        'primary_diagnosis',
        'condition'
    ];

    protected $casts = [
        'date_admitted' => 'datetime',
        'expected_leave' => 'date',
        'actual_leave' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'ward_id');
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class, 'bed_id', 'bed_id');
    }
}