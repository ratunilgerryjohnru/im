<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Bed;
use App\Models\InPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * Display a listing of patients.
     */
    public function index()
    {
        $patients = Patient::orderBy('first_name')->paginate(20);
        return view('patients.index', compact('patients'));
    }

    /**
     * Get patients list for API (JSON)
     */
    public function getPatients(Request $request)
    {
        $query = Patient::query();
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('phone', 'ilike', "%{$search}%")
                  ->orWhere('patient_id', '=', $search);
            });
        }
        
        $patients = $query->orderBy('first_name')->get();
        return response()->json($patients);
    }

    /**
     * Show a single patient
     */
    public function show($id)
    {
        $patient = Patient::with(['inpatients' => function($q) {
            $q->latest()->limit(5);
        }])->findOrFail($id);
        
        if (request()->wantsJson()) {
            return response()->json($patient);
        }
        
        return view('patients.show', compact('patient'));
    }

    /**
     * Store a new patient
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
            'sex' => 'nullable|string|in:Male,Female,Other',
            'marital_status' => 'nullable|string'
        ]);

        $patient = Patient::create($validated);
        
        if (request()->wantsJson()) {
            return response()->json($patient, 201);
        }
        
        return redirect()->route('patients.index')->with('success', 'Patient created successfully');
    }

    /**
     * Update clinical information ONLY (diagnosis and condition)
     */
    public function updateClinical(Request $request, $id)
    {
        try {
            $request->validate([
                'diagnosis' => 'required|string|max:255',
                'condition' => 'nullable|string|max:100'
            ]);

            DB::beginTransaction();

            // Use Eloquent model
            $inpatient = InPatient::where('patient_id', $id)
                ->whereNull('actual_leave')
                ->first();

            if (!$inpatient) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No active inpatient record found for this patient'
                ], 404);
            }

            $inpatient->update([
                'primary_diagnosis' => $request->diagnosis,
                'condition' => $request->condition ?? 'Stable'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Clinical information updated successfully'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . json_encode($e->errors())
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a patient's personal information
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
            'sex' => 'nullable|string|in:Male,Female,Other',
            'marital_status' => 'nullable|string'
        ]);

        $patient->update($validated);
        
        if (request()->wantsJson()) {
            return response()->json($patient);
        }
        
        return redirect()->route('patients.index')->with('success', 'Patient updated successfully');
    }

    /**
     * Delete a patient
     */
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        
        // Check if patient has active admission
        $hasActiveAdmission = InPatient::where('patient_id', $id)->whereNull('actual_leave')->exists();
        
        if ($hasActiveAdmission) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete patient with active admission'], 422);
            }
            return redirect()->route('patients.index')->with('error', 'Cannot delete patient with active admission');
        }
        
        $patient->delete();
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully');
    }

    /**
     * Get patient status (admitted/discharged)
     */
    public function getStatus($id)
    {
        $result = DB::select('SELECT get_patient_status(?::INTEGER)', [$id]);
        
        if (!empty($result)) {
            return response()->json($result[0]->get_patient_status);
        }
        
        $patient = Patient::findOrFail($id);
        return response()->json([
            'patient_id' => $id,
            'name' => $patient->full_name,
            'is_admitted' => false,
            'status' => 'Discharged/Not Admitted'
        ]);
    }

    /**
     * Discharge a patient from admission
     */
    public function discharge(Request $request, $inpatientId)
    {
        try {
            $result = DB::select('SELECT discharge_patient(?::INTEGER, ?::DATE)', [
                $inpatientId,
                $request->discharge_date ?? Carbon::now()->toDateString()
            ]);
            
            $resultData = json_decode(json_encode($result[0]->discharge_patient), true);
            
            if ($resultData['success'] ?? false) {
                return response()->json(['success' => true, 'message' => $resultData['message']]);
            }
            
            return response()->json(['success' => false, 'message' => $resultData['message'] ?? 'Discharge failed'], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Admit a new patient and assign to bed using Supabase procedure
     */
    public function admit(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'age' => 'required|integer|min:0|max:150',
                'diagnosis' => 'required|string',
                'bed_id' => 'required|exists:bed,bed_id',
            ]);

            // Check bed availability using Eloquent
            $bed = Bed::findOrFail($request->bed_id);
            if (!$bed->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected bed is not available'
                ], 422);
            }

            $dateOfBirth = Carbon::now()->subYears($request->age)->toDateString();

            // Create patient using Eloquent
            $patient = Patient::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'dob' => $dateOfBirth,
                'date_registered' => Carbon::now()->toDateString(),
            ]);

            // Call admit_patient function
            $result = DB::select('SELECT admit_patient(?::INTEGER, ?::INTEGER, ?::VARCHAR, ?::VARCHAR)', [
                (int) $patient->patient_id,
                (int) $request->bed_id,
                (string) $request->diagnosis,
                (string) ($request->condition ?? 'Stable')
            ]);

            $resultData = json_decode(json_encode($result[0]->admit_patient), true);

            if ($resultData['success'] ?? false) {
                return response()->json([
                    'success' => true,
                    'message' => $resultData['message'],
                    'patient_id' => $patient->patient_id,
                    'inpatient_id' => $resultData['inpatient_id'] ?? null
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $resultData['message'] ?? 'Admission failed'
            ], 500);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . json_encode($e->errors())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admit an existing patient to a bed using Supabase procedure
     */
    public function admitExisting(Request $request)
    {
        try {
            $request->validate([
                'patient_id' => 'required|exists:patient,patient_id',
                'diagnosis' => 'required|string',
                'condition' => 'nullable|string',
                'bed_id' => 'required|exists:bed,bed_id',
            ]);

            // Check if patient is already admitted
            $isAdmitted = InPatient::where('patient_id', $request->patient_id)
                ->whereNull('actual_leave')
                ->exists();
                
            if ($isAdmitted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient is already admitted'
                ], 422);
            }

            // Check bed availability
            $bed = Bed::findOrFail($request->bed_id);
            if (!$bed->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected bed is not available'
                ], 422);
            }

            $result = DB::select('SELECT admit_patient(?::INTEGER, ?::INTEGER, ?::VARCHAR, ?::VARCHAR)', [
                (int) $request->patient_id,
                (int) $request->bed_id,
                (string) $request->diagnosis,
                (string) ($request->condition ?? 'Stable')
            ]);

            $resultData = json_decode(json_encode($result[0]->admit_patient), true);

            if ($resultData['success'] ?? false) {
                return response()->json([
                    'success' => true,
                    'message' => $resultData['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $resultData['message'] ?? 'Admission failed'
            ], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current admission for a patient
     */
    public function currentAdmission($patientId)
    {
        $admission = InPatient::with(['bed', 'ward'])
            ->where('patient_id', $patientId)
            ->whereNull('actual_leave')
            ->first();
            
        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'No active admission'], 404);
        }
        
        return response()->json(['success' => true, 'data' => $admission]);
    }
}