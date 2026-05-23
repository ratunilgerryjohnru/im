<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\InPatient;
use App\Models\Bed;
use App\Models\PatientMedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
     * Get medical records count with caching
     */
    public function getMedicalRecordsCount()
    {
        $count = Cache::remember('stats_medical_records', 300, function () {
            return PatientMedicalRecord::count();
        });
        
        return response()->json(['count' => $count]);
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
                'medical_records' => PatientMedicalRecord::count(),
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