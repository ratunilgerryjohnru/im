<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $table = 'patient_medical_record';
    protected $primaryKey = 'record_id';
    
    protected $fillable = [
        'record_id',
        'patient_id',
        'diagnosis',
        'allergies',
        'chronic_conditions',
        'blood_type',
        'created_date'
    ];

    protected $casts = [
        'created_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $incrementing = false; // record_id is not auto-incrementing
    protected $keyType = 'int';

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}