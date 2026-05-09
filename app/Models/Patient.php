<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $table = 'patient';
    protected $primaryKey = 'patient_id';

    protected $fillable = [
        'patient_name',
        'address',
        'city',
        'state',
        'postal_code',
        'telephone',
        'date_of_birth',
        'gender',
        'marital_status',
        'emergency_contact',
        'emergency_phone',
        'insurance_company',
        'insurance_number'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Accessor for Age
     */
    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return 'N/A';
        }
        $age = $this->date_of_birth->age;
        return $age . ' years';
    }

    /**
     * Relationship: A patient has many inpatient admissions
     */
    public function inpatients(): HasMany
    {
        return $this->hasMany(Inpatient::class, 'patient_id', 'patient_id');
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
}