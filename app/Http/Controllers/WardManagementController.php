<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\InPatient;
use Illuminate\Http\Request;

class WardManagementController extends Controller
{
    /**
     * Show the Ward & Bed Management page (combines Ward Overview + Patient Admission)
     */
    public function index()
    {
        // Get all wards with bed counts
        $wards = Ward::withCount([
            'beds as total_beds',
            'beds as occupied_beds' => function ($query) {
                $query->where('is_available', false);
            },
            'beds as available_beds' => function ($query) {
                $query->where('is_available', true)
                      ->where(function($q) {
                          $q->whereNull('maintenance_status')
                            ->orWhere('maintenance_status', '!=', 'under_maintenance');
                      });
            }
        ])->get();

        // Get available beds for admission form
        $availableBeds = Bed::where('is_available', true)
            ->where(function($q) {
                $q->whereNull('maintenance_status')
                  ->orWhere('maintenance_status', '!=', 'under_maintenance');
            })
            ->with('ward')
            ->get();

        // OPTIMIZED: Get ONLY patients who are NOT currently admitted
        // First get all admitted patient IDs
        $admittedPatientIds = InPatient::whereNull('actual_leave')->pluck('patient_id')->toArray();
        
        // Then exclude them from patient list
        if (count($admittedPatientIds) > 0) {
            $patients = Patient::whereNotIn('patient_id', $admittedPatientIds)
                ->orderBy('first_name')
                ->get();
        } else {
            $patients = Patient::orderBy('first_name')->get();
        }

        // Calculate overall statistics for occupied & vacant beds tracking
        $totalBedsAll = 0;
        $totalOccupiedAll = 0;
        $totalAvailableAll = 0;

        foreach ($wards as $ward) {
            $totalBedsAll += $ward->total_beds;
            $totalOccupiedAll += $ward->occupied_beds;
            $totalAvailableAll += $ward->available_beds;
        }

        $overallOccupancyRate = $totalBedsAll > 0 ? round(($totalOccupiedAll / $totalBedsAll) * 100) : 0;

        // Get critical wards (occupancy >= 90%)
        $criticalWards = $wards->filter(function($ward) {
            $occupancy = $ward->total_beds > 0 ? round(($ward->occupied_beds / $ward->total_beds) * 100) : 0;
            return $occupancy >= 90;
        });

        return view('wards.management', [
            'wards' => $wards,
            'availableBeds' => $availableBeds,
            'patients' => $patients,
            'totalBedsAll' => $totalBedsAll,
            'totalOccupiedAll' => $totalOccupiedAll,
            'totalAvailableAll' => $totalAvailableAll,
            'overallOccupancyRate' => $overallOccupancyRate,
            'criticalWardsCount' => $criticalWards->count()
        ]);
    }
}