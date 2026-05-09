<?php
// app/Models/MedicalRecord.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    // Specify the correct table name (use your existing table)
    protected $table = 'patient_medical_record';
    
    // Specify the primary key if it's different
    protected $primaryKey = 'record_id';
    
    // Define the fillable fields based on your actual table structure
    protected $fillable = [
        'patient_id',
        'record_type',
        'description',
        'record_date',
        'recorded_by'
    ];

    protected $casts = [
        'record_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}