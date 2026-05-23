<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $table = 'patient_medical_record'; // Matches your Supabase schema
    protected $primaryKey = 'record_id';
    
    protected $fillable = [
        'patient_id',
        'diagnosis',
        'allergies',
        'chronic_conditions',
        'blood_type',
        'created_date',
        'record_type',
        'recorded_by',
        'description'
    ];

    protected $casts = [
        'created_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}