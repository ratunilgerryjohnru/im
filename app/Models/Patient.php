<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patient';
    protected $primaryKey = 'patient_id';
    public $incrementing = false;
    
    protected $fillable = [
        'patient_id',
        'first_name',
        'last_name',
        'address',
        'phone',
        'dob',
        'sex',
        'marital_status',
        'date_registered'
    ];

    public function medicalRecords()
    {
        return $this->hasMany(PatientMedicalRecord::class, 'patient_id', 'patient_id');
    }

    public function inPatients()
    {
        return $this->hasMany(InPatient::class, 'patient_id', 'patient_id');
    }
}