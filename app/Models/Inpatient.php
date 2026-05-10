<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InPatient extends Model
{
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
        'actual_leave' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the patient record associated with this admission.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    /**
     * Get the bed associated with this admission.
     */
    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class, 'bed_id', 'bed_id');
    }

    /**
     * Get the ward associated with this admission.
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'ward_id');
    }
}