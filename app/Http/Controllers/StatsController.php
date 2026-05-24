<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\InPatient;
use App\Models\Bed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * Get total patients count with caching
     */
    public function getTotalPatients()
    {
        $count = Cache::remember('stats_total_patients', 300, function () {
            return Patient::count();
        });
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get active admissions count with caching
     */
    public function getActiveAdmissions()
    {
        $count = Cache::remember('stats_active_admissions', 300, function () {
            return InPatient::whereNull('actual_leave')->count();
        });
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get active admissions with details for the dashboard
     */
    public function getActiveAdmissionsDetails()
    {
        $admissions = DB::table('in_patient')
            ->join('patient', 'in_patient.patient_id', '=', 'patient.patient_id')
            ->join('bed', 'in_patient.bed_id', '=', 'bed.bed_id')
            ->join('ward', 'in_patient.ward_id', '=', 'ward.ward_id')
            ->whereNull('in_patient.actual_leave')
            ->select(
                'in_patient.inpatient_id',
                'in_patient.patient_id',
                'in_patient.date_admitted',
                'in_patient.primary_diagnosis',
                'in_patient.condition',
                DB::raw("CONCAT(patient.first_name, ' ', patient.last_name) as patient_name"),
                'bed.bed_number',
                'ward.ward_name'
            )
            ->get();
        
        return response()->json($admissions);
    }

    /**
     * Get occupied beds count with caching
     */
    public function getOccupiedBeds()
    {
        $count = Cache::remember('stats_occupied_beds', 300, function () {
            return Bed::where('is_available', false)->count();
        });
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get medical records count with caching - FIXED
     */
    public function getMedicalRecordsCount()
    {
        try {
            // Use DB::table directly to avoid model issues
            $count = DB::table('patient_medical_record')->count();
            
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            \Log::error('Medical records count error: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Get all dashboard stats in one call
     */
    public function getAllStats()
    {
        $stats = Cache::remember('dashboard_all_stats', 300, function () {
            return [
                'total_patients' => Patient::count(),
                'active_admissions' => InPatient::whereNull('actual_leave')->count(),
                'occupied_beds' => Bed::where('is_available', false)->count(),
                'available_beds' => Bed::where('is_available', true)->count(),
                'total_beds' => Bed::count(),
                'medical_records' => DB::table('patient_medical_record')->count(),
                'occupancy_rate' => $this->calculateOccupancyRate(),
            ];
        });
        
        return response()->json($stats);
    }

    private function calculateOccupancyRate()
    {
        $totalBeds = Bed::count();
        if ($totalBeds === 0) return 0;
        
        $occupiedBeds = Bed::where('is_available', false)->count();
        return round(($occupiedBeds / $totalBeds) * 100, 2);
    }
}