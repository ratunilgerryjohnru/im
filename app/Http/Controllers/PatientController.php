<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    protected $supabaseUrl;
    protected $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL');
        $this->supabaseKey = env('SUPABASE_KEY');
    }

    public function index()
    {
        return view('patient.index');
    }

    public function getPatients(Request $request)
    {
        try {
            $search = $request->get('search', '');
            
            // Simplified query without medical records
            $query = $this->supabaseUrl . '/rest/v1/patient?select=patient_id,first_name,last_name,phone,address,dob,sex,marital_status,date_registered';
            
            if (!empty($search)) {
                $query .= "&or=(first_name.ilike.%{$search}%,last_name.ilike.%{$search}%,patient_id.eq.{$search},phone.ilike.%{$search}%)";
            }
            
            $query .= "&order=created_at.desc&limit=100";
            
            $response = Http::timeout(30)->withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])->get($query);
            
            if ($response->successful()) {
                return response()->json($response->json());
            }
            
            return response()->json(['error' => 'Failed to fetch patients', 'patients' => []], 500);
            
        } catch (\Exception $e) {
            Log::error('getPatients Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage(), 'patients' => []], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'dob' => 'nullable|date',
                'sex' => 'nullable|string',
                'phone' => 'nullable|string',
                'email' => 'nullable|email',
                'emergency_name' => 'nullable|string',
                'emergency_phone' => 'nullable|string',
                'blood_group' => 'nullable|string',
                'allergies' => 'nullable|string',
                'address' => 'nullable|string',
                'marital_status' => 'nullable|string',
            ]);

            // Get the next patient ID
            $lastResponse = Http::timeout(30)->withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])->get($this->supabaseUrl . '/rest/v1/patient?select=patient_id&order=patient_id.desc&limit=1');
            
            $lastId = 10000;
            if ($lastResponse->successful() && count($lastResponse->json()) > 0) {
                $lastId = $lastResponse->json()[0]['patient_id'];
            }
            $newId = $lastId + 1;
            
            // Insert patient
            $patientData = [
                'patient_id' => $newId,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'dob' => $validated['dob'] ?? null,
                'sex' => $validated['sex'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'marital_status' => $validated['marital_status'] ?? 'Single',
                'date_registered' => date('Y-m-d')
            ];
            
            $patientResponse = Http::timeout(30)->withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Content-Type' => 'application/json',
            ])->post($this->supabaseUrl . '/rest/v1/patient', $patientData);
            
            if (!$patientResponse->successful()) {
                return response()->json(['success' => false, 'message' => 'Failed to create patient'], 500);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Patient registered successfully',
                'patient_id' => $newId
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $response = Http::timeout(30)->withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
                'Content-Type' => 'application/json',
            ])->patch($this->supabaseUrl . '/rest/v1/patient?patient_id=eq.' . $id, $request->all());
            
            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'Patient updated successfully']);
            }
            return response()->json(['success' => false, 'message' => 'Update failed'], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $response = Http::timeout(30)->withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])->delete($this->supabaseUrl . '/rest/v1/patient?patient_id=eq.' . $id);
            
            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'Patient deleted successfully']);
            }
            return response()->json(['success' => false, 'message' => 'Delete failed'], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getStatus($id)
    {
        return response()->json(['current_status' => 'Not admitted']);
    }

    public function getCurrentBed($id)
    {
        return response()->json(['bed_id' => null]);
    }
}