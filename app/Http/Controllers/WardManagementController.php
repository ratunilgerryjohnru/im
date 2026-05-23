<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Bed;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WardManagementController extends Controller
{
    public function index()
    {
        // Get all wards with counts using Eloquent with proper relationships
        $wards = Ward::withCount(['beds as total_beds', 
            'beds as occupied_beds' => function($query) {
                $query->where('is_available', false);
            },
            'beds as available_beds' => function($query) {
                $query->where('is_available', true);
            }
        ])->get();

        // Get patients who are NOT currently admitted
        $admittedPatientIds = DB::table('in_patient')
            ->whereNull('actual_leave')
            ->pluck('patient_id')
            ->toArray();
            
        $patients = Patient::whereNotIn('patient_id', $admittedPatientIds)
            ->orderBy('first_name')
            ->get();

        // Get available beds with ward relationship
        $availableBeds = Bed::readyForPatient()
            ->with('ward')
            ->get();

        // Calculate totals
        $totalBedsAll = $wards->sum('total_beds');
        $totalOccupiedAll = $wards->sum('occupied_beds');
        $totalAvailableAll = $wards->sum('available_beds');
        $overallOccupancyRate = $totalBedsAll > 0 ? round(($totalOccupiedAll / $totalBedsAll) * 100) : 0;

        $criticalWardsCount = $wards->filter(function($ward) {
            $occupancy = $ward->total_beds > 0 ? round(($ward->occupied_beds / $ward->total_beds) * 100) : 0;
            return $occupancy >= 90;
        })->count();

        return view('wards.management', [
            'wards' => $wards,
            'patients' => $patients,
            'availableBeds' => $availableBeds,
            'totalBedsAll' => $totalBedsAll,
            'totalOccupiedAll' => $totalOccupiedAll,
            'totalAvailableAll' => $totalAvailableAll,
            'overallOccupancyRate' => $overallOccupancyRate,
            'criticalWardsCount' => $criticalWardsCount
        ]);
    }
}