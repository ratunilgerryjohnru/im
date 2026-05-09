<?php
// app/Models/PatientMedicalRecord.php

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
        'record_date',
        'doctor_name',
        'diagnosis',
        'treatment',
        'prescription',
        'notes'
    ];
    
    protected $casts = [
        'record_date' => 'date',
    ];
    
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}