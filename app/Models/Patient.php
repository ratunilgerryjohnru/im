<?php
// app/Models/Patient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'first_name',
        'last_name',
        'dob',
        'gender',
        'phone',
        'email',
        'emergency_name',
        'emergency_phone',
        'blood_group',
        'allergies',
        'address',
        'admission_status',
        'bed_occupied'
    ];

    protected $casts = [
        'dob' => 'date',
        'admission_status' => 'boolean',
        'bed_occupied' => 'boolean',
    ];

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }
}