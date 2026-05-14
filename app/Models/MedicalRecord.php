<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientMedicalRecord extends Model
{
    use HasFactory;

    protected $table = 'patient_medical_record';
    protected $primaryKey = 'record_id';
    
    protected $fillable = [
        'patient_id',
        'diagnosis',
        'allergies',
        'chronic_conditions',
        'blood_type',
        'created_date'
    ];

    protected $casts = [
        'created_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}