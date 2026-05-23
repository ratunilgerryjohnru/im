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
        // SINGLE OPTIMIZED QUERY - Get all wards with counts in ONE query
        $wards = Ward::select(
            'ward.*',
            DB::raw('(SELECT COUNT(*) FROM bed WHERE bed.ward_id = ward.ward_id) as total_beds'),
            DB::raw('(SELECT COUNT(*) FROM bed WHERE bed.ward_id = ward.ward_id AND bed.is_available = false) as occupied_beds'),
            DB::raw('(SELECT COUNT(*) FROM bed WHERE bed.ward_id = ward.ward_id AND bed.is_available = true AND (bed.maintenance_status IS NULL OR bed.maintenance_status != \'under_maintenance\')) as available_beds')
        )->get();

        // Get patients who have NEVER been admitted - SINGLE QUERY
        $patients = Patient::whereNotIn('patient_id', function($query) {
            $query->select('patient_id')->from('in_patient');
        })->orderBy('first_name')->get();

        // Get available beds - SINGLE QUERY
        $availableBeds = Bed::where('is_available', true)
            ->where(function($q) {
                $q->whereNull('maintenance_status')
                  ->orWhere('maintenance_status', '!=', 'under_maintenance');
            })
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