<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Bed;
use App\Models\InPatient;
use App\Models\OutPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WardController extends Controller
{
    /**
     * Display a listing of all wards with real-time bed counts.
     */
    public function index()
    {
        $wards = Ward::withCount([
            'beds as total_beds_count',
            'beds as occupied_beds_count' => function ($query) {
                $query->where('is_available', false);
            },
            'beds as available_beds_count' => function ($query) {
                $query->where('is_available', true)
                      ->where(function($q) {
                          $q->whereNull('maintenance_status')
                            ->orWhere('maintenance_status', '!=', 'under_maintenance');
                      });
            },
            'beds as maintenance_beds_count' => function ($query) {
                $query->where('maintenance_status', 'under_maintenance');
            }
        ])->get();

        return view('wards.index', compact('wards'));
    }

    /**
     * Show the form for creating a new ward.
     */
    public function create()
    {
        return view('wards.create');
    }

    /**
     * Show the form for editing a ward.
     */
    public function edit(Ward $ward)
    {
        return view('wards.edit', compact('ward'));
    }

    /**
     * Update the specified ward in storage.
     */
    public function update(Request $request, Ward $ward)
    {
        $validated = $request->validate([
            'ward_name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'ward_type' => 'nullable|string',
            'total_beds' => 'required|integer|min:0',
            'tel_extension' => 'nullable|string|max:10',
            'floor' => 'nullable|string|max:50',
        ]);

        // Update the ward
        $ward->update($validated);

        // Update available beds count
        $this->syncWardAvailableBedsCount($ward->ward_id);

        return redirect()->route('wards.management')->with('success', 'Ward updated successfully');
    }

    /**
     * Remove the specified ward from storage.
     */
    public function destroy(Ward $ward)
    {
        try {
            DB::beginTransaction();
            
            $bedIds = Bed::where('ward_id', $ward->ward_id)->pluck('bed_id');
            
            if ($bedIds->count() > 0) {
                InPatient::whereIn('bed_id', $bedIds)->delete();
            }
            
            Bed::where('ward_id', $ward->ward_id)->delete();
            $ward->delete();
            
            DB::commit();
            
            return redirect()->route('wards.management')->with('success', 'Ward "' . $ward->ward_name . '" deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('wards.management')->with('error', 'Error deleting ward: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified bed from storage.
     */
    public function destroyBed($id)
    {
        try {
            DB::beginTransaction();
            
            $bed = Bed::findOrFail($id);
            $wardId = $bed->ward_id;
            
            InPatient::where('bed_id', $id)->delete();
            $bed->delete();
            
            $this->syncWardAvailableBedsCount($wardId);
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Bed deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error deleting bed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the Bed Management screen for a specific ward.
     */
    public function show(Ward $ward)
    {
        $ward->load(['beds.currentInpatient.patient']);
        
        $bedsCollection = $ward->beds;

        $availableBeds = $ward->beds()
            ->where('is_available', true)
            ->where(function($q) {
                $q->whereNull('maintenance_status')
                  ->orWhere('maintenance_status', '!=', 'under_maintenance');
            })
            ->get();

        $stats = [
            'all' => $bedsCollection->count(),
            'available' => $bedsCollection->filter(function($bed) {
                return $bed->is_available === true && 
                       ($bed->maintenance_status !== 'under_maintenance');
            })->count(),
            'occupied' => $bedsCollection->where('is_available', false)
                                         ->where('maintenance_status', '!=', 'under_maintenance')
                                         ->count(),
            'maintenance' => $bedsCollection->where('maintenance_status', 'under_maintenance')->count(),
            'reserved' => 0,
        ];

        return view('wards.show', [
            'ward' => $ward,
            'beds' => $bedsCollection,
            'stats' => $stats,
            'availableBeds' => $availableBeds
        ]);
    }

    /**
     * Store a newly created bed for a specific ward.
     */
    public function storeBed(Request $request, $ward_id)
    {
        $request->validate([
            'bed_number' => 'required|string|max:255',
        ]);

        try {
            $ward = Ward::withCount('beds')->findOrFail($ward_id);
            
            $operationalBedsCount = $ward->beds()
                ->where(function($q) {
                    $q->whereNull('maintenance_status')
                      ->orWhere('maintenance_status', '!=', 'under_maintenance');
                })
                ->count();

            if ($operationalBedsCount >= $ward->total_beds) {
                return response()->json([
                    'success' => false, 
                    'message' => "Capacity Reached! This ward is maxed out at {$ward->total_beds} operational beds."
                ], 422);
            }

            Bed::create([
                'bed_number' => $request->bed_number,
                'ward_id' => $ward_id,
                'bed_type' => 'Standard',
                'is_available' => true,
                'maintenance_status' => 'operational'
            ]);

            $this->syncWardAvailableBedsCount($ward_id);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Bed Status (Only Available/Occupied - removed Maintenance)
     */
    public function updateBedStatus(Request $request, $id)
    {
        try {
            $bed = Bed::findOrFail($id);
            
            $oldAvailability = $bed->is_available;
            
            switch ($request->status) {
                case 'available':
                    $bed->update([
                        'is_available' => true,
                        'maintenance_status' => 'operational'
                    ]);
                    break;
                case 'occupied':
                    $bed->update([
                        'is_available' => false,
                        'maintenance_status' => 'operational'
                    ]);
                    break;
            }
            
            if ($oldAvailability != $bed->is_available) {
                $this->syncWardAvailableBedsCount($bed->ward_id);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handles Bed Updates (Assign/Discharge)
     */
    public function updateBed(Request $request, $id)
    {
        try {
            return DB::transaction(function () use ($request, $id) {
                $bed = Bed::findOrFail($id);

                if ($request->action === 'discharge') {
                    $inpatient = InPatient::where('bed_id', $id)
                        ->whereNull('actual_leave')
                        ->first();

                    if (!$inpatient) {
                        throw new \Exception("No active patient found in this bed.");
                    }

                    // Update inpatient record with discharge date
                    $inpatient->update([
                        'actual_leave' => now(),
                    ]);

                    // Create outpatient record
                    OutPatient::create([
                        'patient_id' => $inpatient->patient_id,
                        'date' => now()->toDateString(),
                        'time' => now()->toTimeString(),
                    ]);

                    // Free up the bed
                    $bed->update([
                        'is_available' => true,
                    ]);

                    $this->syncWardAvailableBedsCount($bed->ward_id);

                    return response()->json(['success' => true, 'message' => 'Discharged successfully.']);
                }

                if ($request->action === 'assign') {
                    $request->validate([
                        'patient_id' => 'required',
                        'diagnosis' => 'nullable|string',
                        'condition' => 'nullable|string'
                    ]);

                    if (!$bed->is_available) {
                        throw new \Exception("Bed is already occupied.");
                    }

                    InPatient::create([
                        'patient_id' => $request->patient_id,
                        'ward_id' => $bed->ward_id,
                        'bed_id' => $bed->bed_id,
                        'date_admitted' => now(),
                        'primary_diagnosis' => $request->diagnosis ?? 'Standard Care',
                        'condition' => $request->condition ?? 'Stable'
                    ]);

                    $bed->update(['is_available' => false]);

                    $this->syncWardAvailableBedsCount($bed->ward_id);

                    return response()->json(['success' => true, 'message' => 'Assigned successfully.']);
                }

                throw new \Exception("Invalid action.");
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created ward.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ward_name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'ward_type' => 'nullable|string',
            'total_beds' => 'required|integer|min:0',
            'tel_extension' => 'nullable|string|max:10',
            'floor' => 'nullable|string|max:50',
        ]);

        // Create ward
        $ward = Ward::create($validated);
        
        // Set available_beds to 0 since no beds exist yet
        $ward->update(['available_beds' => 0]);

        return redirect()->route('wards.management')->with('success', 'Ward created successfully');
    }

    /**
     * Helper method to sync ward's available_beds column
     */
    private function syncWardAvailableBedsCount($wardId)
    {
        $availableCount = Bed::where('ward_id', $wardId)
            ->where('is_available', true)
            ->count();
        
        Ward::where('ward_id', $wardId)->update(['available_beds' => $availableCount]);
    }
}