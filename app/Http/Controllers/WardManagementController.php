<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Bed;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class WardManagementController extends Controller
{
    public function index()
    {
        $cacheKey = 'ward_management_cached_data_v3';
        
        $data = Cache::remember($cacheKey, 1800, function () {
            $wards = Ward::all();
            
            $wardsData = [];
            
            foreach ($wards as $ward) {
                $totalBeds = Bed::where('ward_id', $ward->ward_id)->count();
                $occupiedCount = Bed::where('ward_id', $ward->ward_id)
                    ->where('is_available', false)
                    ->count();
                $availableCount = Bed::where('ward_id', $ward->ward_id)
                    ->where('is_available', true)
                    ->where(function($q) {
                        $q->whereNull('maintenance_status')
                            ->orWhere('maintenance_status', '!=', 'under_maintenance');
                    })
                    ->count();
                $occupancyRate = $totalBeds > 0 ? round(($occupiedCount / $totalBeds) * 100) : 0;
                
                // Use ARRAYS instead of objects for cache safety
                $wardsData[] = [
                    'ward_id' => $ward->ward_id,
                    'ward_name' => $ward->ward_name,
                    'location' => $ward->location,
                    'ward_type' => $ward->ward_type,
                    'total_beds' => $totalBeds,
                    'occupied_beds' => $occupiedCount,
                    'available_beds' => $availableCount,
                    'occupancy_rate' => $occupancyRate,
                    'floor' => $ward->floor ?? '1',
                    'tel_extension' => $ward->tel_extension,
                    'created_at' => $ward->created_at,
                    'updated_at' => $ward->updated_at,
                ];
            }

            $patients = DB::select("
                SELECT patient_id, first_name, last_name
                FROM patient
                WHERE patient_id NOT IN (SELECT DISTINCT patient_id FROM in_patient)
                ORDER BY first_name
                LIMIT 50
            ");
            $patients = collect($patients);

            $availableBeds = Bed::where('is_available', true)
                ->where(function($q) {
                    $q->whereNull('maintenance_status')
                      ->orWhere('maintenance_status', '!=', 'under_maintenance');
                })
                ->with('ward')
                ->limit(100)
                ->get();

            $totalBedsAll = collect($wardsData)->sum('total_beds');
            $totalOccupiedAll = collect($wardsData)->sum('occupied_beds');
            $totalAvailableAll = collect($wardsData)->sum('available_beds');
            $overallOccupancyRate = $totalBedsAll > 0 ? round(($totalOccupiedAll / $totalBedsAll) * 100) : 0;

            $criticalWardsCount = collect($wardsData)->filter(function($ward) {
                return $ward['occupancy_rate'] >= 90;
            })->count();

            return [
                'wards' => $wardsData,
                'patients' => $patients,
                'availableBeds' => $availableBeds,
                'totalBedsAll' => $totalBedsAll,
                'totalOccupiedAll' => $totalOccupiedAll,
                'totalAvailableAll' => $totalAvailableAll,
                'overallOccupancyRate' => $overallOccupancyRate,
                'criticalWardsCount' => $criticalWardsCount
            ];
        });
        
        return view('wards.management', $data);
    }
    
    public static function clearCache()
    {
        Cache::forget('ward_management_cached_data_v3');
        Cache::forget('ward_management_cached_data');
        Cache::forget('ward_management_cached_data_v2');
    }
}