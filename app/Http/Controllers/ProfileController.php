<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StatsController extends Controller
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
            ])->post($this->supabaseUrl . '/rest/v1/rpc/' . $functionName, $params);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
            return ['success' => false, 'message' => $response->body()];
        } catch (\Exception $e) {
            Log::error('Stats Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Display dashboard view
     */
    public function dashboard()
    {
        return view('dashboard');
    }

    /**
     * Get total patients count
     */
    public function getTotalPatients()
    {
        $result = $this->callRpc('get_total_patients_count', []);
        
        if ($result['success']) {
            return response()->json(['count' => $result['data']]);
        }
        return response()->json(['count' => 0, 'error' => $result['message']]);
    }

    /**
     * Get active admissions count
     */
    public function getActiveAdmissions()
    {
        $result = $this->callRpc('get_active_admissions_count', []);
        
        if ($result['success']) {
            return response()->json(['count' => $result['data']]);
        }
        return response()->json(['count' => 0, 'error' => $result['message']]);
    }

    /**
     * Get occupied beds count
     */
    public function getOccupiedBeds()
    {
        $result = $this->callRpc('get_occupied_beds_count', []);
        
        if ($result['success']) {
            return response()->json(['count' => $result['data']]);
        }
        return response()->json(['count' => 0, 'error' => $result['message']]);
    }

    /**
     * Get medical records count
     */
    public function getMedicalRecordsCount()
    {
        $result = $this->callRpc('get_medical_records_count', []);
        
        if ($result['success']) {
            return response()->json(['count' => $result['data']]);
        }
        return response()->json(['count' => 0, 'error' => $result['message']]);
    }

    /**
     * Get all dashboard stats in one call
     */
    public function getAllStats()
    {
        $totalPatients = $this->callRpc('get_total_patients_count', []);
        $activeAdmissions = $this->callRpc('get_active_admissions_count', []);
        $occupiedBeds = $this->callRpc('get_occupied_beds_count', []);
        $medicalRecords = $this->callRpc('get_medical_records_count', []);

        return response()->json([
            'total_patients' => $totalPatients['success'] ? $totalPatients['data'] : 0,
            'active_admissions' => $activeAdmissions['success'] ? $activeAdmissions['data'] : 0,
            'occupied_beds' => $occupiedBeds['success'] ? $occupiedBeds['data'] : 0,
            'medical_records' => $medicalRecords['success'] ? $medicalRecords['data'] : 0,
        ]);
    }
}