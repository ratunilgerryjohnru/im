<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdmissionController extends Controller
{
    protected $supabaseUrl;
    protected $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL');
        $this->supabaseKey = env('SUPABASE_KEY');
    }

    /**
     * Call a Supabase RPC function
     */
    private function callRpc($functionName, $params = [])
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Content-Type' => 'application/json',
                'Prefer' => 'params=single-object'
            ])->post($this->supabaseUrl . '/rest/v1/rpc/' . $functionName, $params);

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && isset($data[0])) {
                    return ['success' => true, 'data' => $data[0]];
                }
                return ['success' => true, 'data' => $data];
            } else {
                Log::error('Admission RPC Error: ' . $response->body());
                return ['success' => false, 'message' => $response->body()];
            }
        } catch (\Exception $e) {
            Log::error('Admission Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Admit a patient to a bed/ward
     */
    public function admit(Request $request)
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|integer',
                'bed_id' => 'required|integer',
                'ward_id' => 'required|integer',
                'primary_diagnosis' => 'nullable|string',
            ]);

            // First check if patient is already admitted
            $statusCheck = $this->callRpc('get_patient_status', ['p_patient_id' => $validated['patient_id']]);
            
            if ($statusCheck['success'] && $statusCheck['data']) {
                $status = $statusCheck['data'];
                if (isset($status->current_status) && $status->current_status === 'Admitted') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Patient is already admitted'
                    ], 400);
                }
            }

            // Call admit_patient function
            $result = $this->callRpc('admit_patient', [
                'p_patient_id' => $validated['patient_id'],
                'p_bed_id' => $validated['bed_id'],
                'p_ward_id' => $validated['ward_id'],
                'p_primary_diagnosis' => $validated['primary_diagnosis'] ?? null
            ]);

            if ($result['success'] && $result['data']) {
                return response()->json([
                    'success' => $result['data']->success ?? true,
                    'message' => $result['data']->message ?? 'Patient admitted successfully',
                    'data' => $result['data']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Admission failed'
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Discharge a patient
     */
    public function discharge(Request $request, $inpatientId)
    {
        try {
            $validated = $request->validate([
                'discharge_date' => 'nullable|date'
            ]);

            $result = $this->callRpc('discharge_patient', [
                'p_inpatient_id' => (int)$inpatientId,
                'p_actual_discharge_date' => $validated['discharge_date'] ?? now()->toDateString()
            ]);

            if ($result['success'] && $result['data']) {
                return response()->json([
                    'success' => $result['data']->success ?? true,
                    'message' => $result['data']->message ?? 'Patient discharged successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Discharge failed'
            ], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get patient admission status
     */
    public function getPatientStatus($patientId)
    {
        $result = $this->callRpc('get_patient_status', ['p_patient_id' => (int)$patientId]);
        
        if ($result['success']) {
            return response()->json($result['data']);
        }
        
        return response()->json(['error' => $result['message']], 500);
    }
}