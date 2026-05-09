<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Bed;
use App\Models\Inpatient;
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
            'ward_type' => 'required|string',
            'total_beds' => 'required|integer|min:1',
            'floor' => 'nullable|string|max:50',
            'dept_id' => 'nullable|exists:department,dept_id'
        ]);

        // Check if total_beds changed
        $oldTotalBeds = $ward->total_beds;
        $newTotalBeds = $validated['total_beds'];

        $ward->update($validated);

        // If total_beds increased, add new beds
        if ($newTotalBeds > $oldTotalBeds) {
            $bedsToAdd = $newTotalBeds - $oldTotalBeds;
            $currentBedCount = $ward->beds()->count();
            
            for ($i = 1; $i <= $bedsToAdd; $i++) {
                $newNumber = $currentBedCount + $i;
                Bed::create([
                    'bed_name' => "B{$ward->ward_id}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT),
                    'ward_id' => $ward->ward_id,
                    'bed_type' => 'Standard',
                    'is_available' => true,
                    'maintenance_status' => 'operational'
                ]);
            }
        }

        // Update available beds count
        $this->syncWardAvailableBedsCount($ward->ward_id);

        return redirect()->route('wards.management')->with('success', 'Ward updated successfully');
    }

    /**
     * Remove the specified ward from storage.
     * This will delete all beds and associated inpatients first.
     */
    public function destroy(Ward $ward)
    {
        try {
            DB::beginTransaction();
            
            // Get all bed IDs in this ward
            $bedIds = Bed::where('ward_id', $ward->ward_id)->pluck('bed_id');
            
            // Delete all inpatients assigned to these beds
            if ($bedIds->count() > 0) {
                Inpatient::whereIn('bed_id', $bedIds)->delete();
            }
            
            // Delete all beds in this ward
            Bed::where('ward_id', $ward->ward_id)->delete();
            
            // Delete the ward
            $ward->delete();
            
            DB::commit();
            
            return redirect()->route('wards.management')->with('success', 'Ward "' . $ward->ward_name . '" and all associated data deleted successfully');
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
            
            // Delete any inpatient assigned to this bed first
            Inpatient::where('bed_id', $id)->delete();
            
            // Delete the bed
            $bed->delete();
            
            // Sync ward available beds count
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

        // Get available beds for admission form
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
            'bed_name' => 'required|string|max:255',
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
                'bed_name' => $request->bed_name,
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
     * Update Bed Status (Maintenance/Available/Occupied)
     */
    public function updateBedStatus(Request $request, $id)
    {
        try {
            $bed = Bed::findOrFail($id);
            
            $oldAvailability = $bed->is_available;
            $oldMaintenanceStatus = $bed->maintenance_status;
            
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
                case 'maintenance':
                    $bed->update([
                        'is_available' => false,
                        'maintenance_status' => 'under_maintenance'
                    ]);
                    break;
            }
            
            if ($oldAvailability != $bed->is_available || 
                $oldMaintenanceStatus != $bed->maintenance_status) {
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
                    $inpatient = Inpatient::where('bed_id', $id)
                        ->whereNull('discharge_date')
                        ->first();

                    if (!$inpatient) {
                        throw new \Exception("No active patient found in this bed.");
                    }

                    $inpatient->update([
                        'discharge_date' => now(),
                    ]);

                    $bed->update([
                        'is_available' => true,
                        'last_cleaned' => now()
                    ]);

                    $this->syncWardAvailableBedsCount($bed->ward_id);

                    return response()->json(['success' => true, 'message' => 'Discharged successfully.']);
                }

                if ($request->action === 'assign') {
                    $request->validate([
                        'patient_id' => 'required',
                        'diagnosis' => 'nullable|string'
                    ]);

                    if (!$bed->is_available) {
                        throw new \Exception("Bed is already occupied.");
                    }

                    Inpatient::create([
                        'patient_id' => $request->patient_id,
                        'ward_id' => $bed->ward_id,
                        'bed_id' => $bed->bed_id,
                        'admission_date' => now(),
                        'primary_diagnosis' => $request->diagnosis ?? 'Standard Care'
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
            'ward_type' => 'required|string',
            'total_beds' => 'required|integer|min:1',
            'floor' => 'nullable|string|max:50',
            'dept_id' => 'nullable|exists:department,dept_id'
        ]);

        $ward = Ward::create($validated);
        
        for ($i = 1; $i <= $validated['total_beds']; $i++) {
            Bed::create([
                'bed_name' => "B{$ward->ward_id}-" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'ward_id' => $ward->ward_id,
                'bed_type' => 'Standard',
                'is_available' => true,
                'maintenance_status' => 'operational'
            ]);
        }
        
        $ward->update(['available_beds' => $validated['total_beds']]);

        return redirect()->route('wards.management')->with('success', 'Ward created successfully');
    }

    /**
     * Helper method to sync ward's available_beds column with actual bed records
     */
    private function syncWardAvailableBedsCount($wardId)
    {
        $availableCount = Bed::where('ward_id', $wardId)
            ->where('is_available', true)
            ->where(function($q) {
                $q->whereNull('maintenance_status')
                  ->orWhere('maintenance_status', '!=', 'under_maintenance');
            })
            ->count();
        
        Ward::where('ward_id', $wardId)->update(['available_beds' => $availableCount]);
    }
}