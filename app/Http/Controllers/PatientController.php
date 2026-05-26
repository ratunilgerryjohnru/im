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
        $patients = Patient::orderBy('patient_id', 'asc')->paginate(20);
        return view('patients.index', compact('patients'));
    }

    /**
     * Get patients list for API (JSON) - ORDER BY patient_id ASC (oldest first)
     */
    public function getPatients(Request $request)
    {
        try {
            $query = Patient::query();

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;

                // Check if search is numeric (for patient_id)
                if (is_numeric($search)) {
                    $query->where('patient_id', '=', (int) $search);
                } else {
                    // Use LIKE for better compatibility
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%");
                    });
                }
            }

            // Order by patient_id ascending (oldest first)
            $patients = $query->orderBy('patient_id', 'asc')->get();

            return response()->json($patients);

        } catch (\Exception $e) {
            \Log::error('getPatients error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a single patient with all related data
     */
    public function show($id)
    {
        $patient = Patient::with([
            'inpatients' => function ($q) {
                $q->latest('date_admitted');
            },
            'medicalRecords'
        ])->findOrFail($id);

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
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'dob' => 'nullable|date',
                'sex' => 'nullable|string|in:Male,Female,Other',
                'marital_status' => 'nullable|string',
                'date_registered' => 'nullable|date'
            ]);

            // Set date_registered if not provided
            if (!isset($validated['date_registered'])) {
                $validated['date_registered'] = Carbon::now()->toDateString();
            }

            $patient = Patient::create($validated);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'patient_id' => $patient->patient_id,
                    'patient' => $patient
                ], 201);
            }

            return redirect()->route('patients.index')->with('success', 'Patient created successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating patient: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Update clinical information ONLY (diagnosis and condition) - FULLY FIXED
     */
    public function updateClinical(Request $request, $id)
    {
        try {
            $request->validate([
                'diagnosis' => 'required|string|max:255',
                'condition' => 'nullable|string|max:100'
            ]);

            // Save to medical records table
            $maxId = DB::table('patient_medical_record')->max('record_id') ?? 0;

            DB::table('patient_medical_record')->insert([
                'record_id' => $maxId + 1,
                'patient_id' => $id,
                'diagnosis' => $request->diagnosis,
                'chronic_conditions' => $request->condition ?? 'Not specified',
                'created_date' => now()->toDateString(),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Clinical information saved to medical records'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a patient's personal information
     */
    public function update(Request $request, $id)
    {
        try {
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
                return response()->json([
                    'success' => true,
                    'patient' => $patient
                ]);
            }

            return redirect()->route('patients.index')->with('success', 'Patient updated successfully');

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Delete a patient
     */
    public function destroy($id)
    {
        try {
            $patient = Patient::findOrFail($id);

            // Check if patient has active admission
            $hasActiveAdmission = InPatient::where('patient_id', $id)->whereNull('actual_leave')->exists();

            if ($hasActiveAdmission) {
                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete patient with active admission'
                    ], 422);
                }
                return redirect()->route('patients.index')->with('error', 'Cannot delete patient with active admission');
            }

            $patient->delete();

            if (request()->wantsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('patients.index')->with('success', 'Patient deleted successfully');

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Get patient status (admitted/discharged)
     */
    public function getStatus($id)
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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
                return response()->json([
                    'success' => true,
                    'message' => $resultData['message']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $resultData['message'] ?? 'Discharge failed'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
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

            $bed = Bed::findOrFail($request->bed_id);
            if (!$bed->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected bed is not available'
                ], 422);
            }

            $dateOfBirth = Carbon::now()->subYears($request->age)->toDateString();

            $patient = Patient::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'dob' => $dateOfBirth,
                'date_registered' => Carbon::now()->toDateString(),
            ]);

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

            // Call admit_patient function
            $result = DB::select('SELECT admit_patient(?::INTEGER, ?::INTEGER, ?::VARCHAR, ?::VARCHAR)', [
                (int) $request->patient_id,
                (int) $request->bed_id,
                (string) $request->diagnosis,
                (string) ($request->condition ?? 'Stable')
            ]);

            if (!empty($result)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Patient admitted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Admission failed - no response'
            ], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . json_encode($e->errors())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Admit existing error: ' . $e->getMessage());
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
        try {
            $admission = InPatient::with(['bed', 'ward'])
                ->where('patient_id', $patientId)
                ->whereNull('actual_leave')
                ->first();

            if (!$admission) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active admission'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $admission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search for patients (for dashboard)
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q');

            if (empty($query)) {
                return response()->json([]);
            }

            $patients = Patient::where('first_name', 'LIKE', "%{$query}%")
                ->orWhere('last_name', 'LIKE', "%{$query}%")
                ->orWhere('phone', 'LIKE', "%{$query}%")
                ->orWhere('patient_id', '=', (int) $query)
                ->orderBy('first_name')
                ->limit(20)
                ->get();

            return response()->json($patients);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get full patient details including admission and medical records
     */
    public function getFullDetails($id)
    {
        try {
            $patient = Patient::findOrFail($id);

            $admission = DB::table('in_patient')
                ->leftJoin('bed', 'in_patient.bed_id', '=', 'bed.bed_id')
                ->leftJoin('ward', 'in_patient.ward_id', '=', 'ward.ward_id')
                ->where('in_patient.patient_id', $id)
                ->whereNull('in_patient.actual_leave')
                ->select(
                    'in_patient.inpatient_id',
                    'in_patient.date_admitted',
                    'in_patient.primary_diagnosis',
                    'in_patient.condition',
                    'bed.bed_number',
                    'ward.ward_name'
                )
                ->first();

            $currentAdmission = null;
            if ($admission) {
                $currentAdmission = [
                    'inpatient_id' => $admission->inpatient_id,
                    'date_admitted' => $admission->date_admitted,
                    'primary_diagnosis' => $admission->primary_diagnosis,
                    'condition' => $admission->condition,
                    'bed_number' => $admission->bed_number,
                    'ward_name' => $admission->ward_name
                ];
            }

            $medicalRecords = DB::table('patient_medical_record')
                ->where('patient_id', $id)
                ->orderBy('created_date', 'desc')
                ->get();

            return response()->json([
                'patient' => $patient,
                'current_admission' => $currentAdmission,
                'medical_records' => $medicalRecords
            ]);

        } catch (\Exception $e) {
            \Log::error('Get full details error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}