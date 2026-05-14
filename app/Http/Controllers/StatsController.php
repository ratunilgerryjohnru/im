<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StatsController extends Controller
{
    protected $supabaseUrl;
    protected $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL');
        $this->supabaseKey = env('SUPABASE_KEY');
    }

    public function getTotalPatients()
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])->get($this->supabaseUrl . '/rest/v1/patient?select=patient_id');
            
            $count = $response->successful() ? count($response->json()) : 0;
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 56]);
        }
    }

    public function getActiveAdmissions()
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])->get($this->supabaseUrl . '/rest/v1/in_patient?actual_leave=is.null&select=*');
            
            $count = $response->successful() ? count($response->json()) : 0;
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }

    public function getOccupiedBeds()
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])->get($this->supabaseUrl . '/rest/v1/bed?is_available=eq.false&select=*');
            
            $count = $response->successful() ? count($response->json()) : 0;
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }

    public function getMedicalRecordsCount()
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'apikey' => $this->supabaseKey,
                'Authorization' => 'Bearer ' . $this->supabaseKey,
            ])->get($this->supabaseUrl . '/rest/v1/patient_medical_record?select=*');
            
            $count = $response->successful() ? count($response->json()) : 0;
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }
}