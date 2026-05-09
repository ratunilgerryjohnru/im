<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::orderBy('created_at', 'desc')->get();
        $medicalRecords = MedicalRecord::with('patient')->orderBy('record_date', 'desc')->get();
        
        $stats = [
            'total_patients' => Patient::count(),
            'active_admissions' => Patient::where('admission_status', true)->count(),
            'occupied_beds' => Patient::where('bed_occupied', true)->count(),
            'medical_records' => MedicalRecord::count(),
        ];
        
        return view('index', compact('patients', 'medicalRecords', 'stats'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'dob' => 'required|date',
                'gender' => 'required|string',
                'phone' => 'required|string',
                'email' => 'nullable|email',
                'emergency_name' => 'required|string',
                'emergency_phone' => 'required|string',
                'blood_group' => 'required|string',
                'allergies' => 'required|string',
                'address' => 'required|string',
            ]);

            // Generate unique patient ID
            $lastPatient = Patient::orderBy('patient_id', 'desc')->first();
            $lastNumber = $lastPatient ? $lastPatient->patient_id : 1000;
            $patientId = 'P-' . ($lastNumber + 1);

            $patient = Patient::create(array_merge($validated, [
                'patient_id' => $patientId,
                'admission_status' => false,
                'bed_occupied' => false,
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Patient registered successfully',
                'patient' => $patient
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPatients(Request $request)
    {
        $search = $request->get('search', '');
        $query = Patient::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $patients = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json($patients);
    }

    public function toggleAdmission($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->admission_status = !$patient->admission_status;
        $patient->save();
        
        return response()->json([
            'success' => true,
            'admission_status' => $patient->admission_status
        ]);
    }

    public function toggleBed($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->bed_occupied = !$patient->bed_occupied;
        $patient->save();
        
        return response()->json([
            'success' => true,
            'bed_occupied' => $patient->bed_occupied
        ]);
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();
        
        return response()->json(['success' => true]);
    }

    public function addMedicalRecord(Request $request)
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|exists:patient,id',
                'record_date' => 'required|date',
                'record_type' => 'required|string',
                'description' => 'required|string',
                'recorded_by' => 'required|string',
            ]);

            $record = MedicalRecord::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Medical record added successfully',
                'record' => $record->load('patient')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMedicalRecords()
    {
        $records = MedicalRecord::with('patient')
            ->orderBy('record_date', 'desc')
            ->get();
        
        return response()->json($records);
    }

    public function deleteMedicalRecord($id)
    {
        try {
            $record = MedicalRecord::findOrFail($id);
            $record->delete();
            
            return response()->json(['success' => true, 'message' => 'Record deleted']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting record'], 500);
        }
    }

    public function getStats()
    {
        return response()->json([
            'total_patients' => Patient::count(),
            'active_admissions' => Patient::where('admission_status', true)->count(),
            'occupied_beds' => Patient::where('bed_occupied', true)->count(),
            'medical_records' => MedicalRecord::count(),
        ]);
    }
}