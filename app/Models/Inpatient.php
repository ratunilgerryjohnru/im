<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inpatient extends Model
{
    protected $table = 'inpatient';
    protected $primaryKey = 'inpatient_id';

    protected $fillable = [
        'patient_id',
        'admission_date',
        'discharge_date',
        'ward_id',
        'bed_id',
        'admission_reason',
        'discharge_notes',
        'primary_diagnosis',
        'admission_type',
        'condition'  // ADDED condition field
    ];

    protected $casts = [
        'admission_date' => 'datetime',
        'discharge_date' => 'datetime',
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