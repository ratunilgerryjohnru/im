<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $table = 'patient';
    protected $primaryKey = 'patient_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'phone',
        'dob',
        'sex',
        'marital_status',
        'date_registered'
    ];

    protected $casts = [
        'dob' => 'date',
        'date_registered' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Accessor for full name
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Accessor for Age
     */
    public function getAgeAttribute()
    {
        if (!$this->dob) {
            return 'N/A';
        }
        $age = $this->dob->age;
        return $age . ' years';
    }

    /**
     * Accessor for patient_name (for backward compatibility)
     */
    public function getPatientNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Relationship: A patient has many inpatient admissions
     */
    public function inpatients(): HasMany
    {
        return $this->hasMany(InPatient::class, 'patient_id', 'patient_id');
    }

    /**
     * Relationship: A patient has many appointments
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id', 'patient_id');
    }

    /**
     * Relationship: A patient has many bills
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class, 'patient_id', 'patient_id');
    }

    /**
     * Relationship: A patient has medical records
     * Using MedicalRecord model
     */
    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'patient_id', 'patient_id');
    }
}