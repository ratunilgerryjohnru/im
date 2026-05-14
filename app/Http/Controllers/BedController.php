<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BedController extends Controller
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
            Log::error('Bed Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get available beds (optionally by ward)
     */
    public function getAvailableBeds($wardId = null)
    {
        if ($wardId) {
            $result = $this->callRpc('get_available_beds', ['ward_id_param' => (int)$wardId]);
        } else {
            // Get all wards stats which includes available beds
            $result = $this->callRpc('get_all_wards_stats', []);
        }
        
        if ($result['success']) {
            return response()->json($result['data']);
        }
        
        return response()->json(['error' => $result['message']], 500);
    }

    /**
     * Get stats for a specific ward
     */
    public function getWardStats($wardId)
    {
        $result = $this->callRpc('get_ward_stats', ['ward_id_param' => (int)$wardId]);
        
        if ($result['success']) {
            return response()->json($result['data']);
        }
        
        return response()->json(['error' => $result['message']], 500);
    }

    /**
     * Get stats for all wards
     */
    public function getAllWardsStats()
    {
        $result = $this->callRpc('get_all_wards_stats', []);
        
        if ($result['success']) {
            return response()->json($result['data']);
        }
        
        return response()->json(['error' => $result['message']], 500);
    }

    /**
     * Get patient's current bed
     */
    public function getPatientCurrentBed($patientId)
    {
        $result = $this->callRpc('get_patient_current_bed', ['p_patient_id' => (int)$patientId]);
        
        if ($result['success']) {
            return response()->json($result['data']);
        }
        
        return response()->json(['error' => $result['message']], 500);
    }
}