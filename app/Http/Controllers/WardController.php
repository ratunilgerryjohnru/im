<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\InPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WardController extends Controller
{
    public function index()
    {
        $wards = DB::select('SELECT * FROM get_all_wards_stats()');
        
        // Map database column names to blade view expected names
        foreach ($wards as $ward) {
            $ward->total_beds_count = $ward->total_beds ?? 0;
            $ward->occupied_beds_count = $ward->occupied_beds ?? 0;
            $ward->available_beds_count = $ward->available_beds ?? 0;
        }
        
        return view('wards.index', compact('wards'));
    }

    public function create()
    {
        return view('wards.create');
    }

    public function edit(Ward $ward)
    {
        return view('wards.edit', compact('ward'));
    }

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

        $ward->update($validated);
        
        return redirect()->route('wards.management')->with('success', 'Ward updated successfully');
    }

    public function destroy(Ward $ward)
    {
        try {
            DB::beginTransaction();
            
            // First discharge any active patients before deleting
            $activeInpatients = DB::table('in_patient')
                ->whereIn('bed_id', function($q) use ($ward) {
                    $q->select('bed_id')->from('bed')->where('ward_id', $ward->ward_id);
                })
                ->whereNull('actual_leave')
                ->get();
            
            foreach ($activeInpatients as $inpatient) {
                DB::select('SELECT discharge_patient(?::INTEGER)', [$inpatient->inpatient_id]);
            }
            
            // Delete inpatient records (already discharged)
            DB::table('in_patient')->whereIn('bed_id', function($q) use ($ward) {
                $q->select('bed_id')->from('bed')->where('ward_id', $ward->ward_id);
            })->delete();
            
            DB::table('bed')->where('ward_id', $ward->ward_id)->delete();
            $ward->delete();
            
            DB::commit();
            
            return redirect()->route('wards.management')->with('success', 'Ward deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('wards.management')->with('error', 'Error deleting ward: ' . $e->getMessage());
        }
    }

    public function destroyBed($id)
    {
        try {
            DB::beginTransaction();
            
            // Check if bed has active patient
            $activeInpatient = DB::table('in_patient')
                ->where('bed_id', $id)
                ->whereNull('actual_leave')
                ->first();
            
            if ($activeInpatient) {
                // Discharge patient first
                DB::select('SELECT discharge_patient(?::INTEGER)', [$activeInpatient->inpatient_id]);
            }
            
            DB::table('in_patient')->where('bed_id', $id)->delete();
            DB::table('bed')->where('bed_id', $id)->delete();
            
            DB::commit();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Ward $ward)
    {
        $totalBeds = $ward->beds()->count();
        
        $occupiedCount = DB::table('bed')
            ->join('in_patient', 'bed.bed_id', '=', 'in_patient.bed_id')
            ->where('bed.ward_id', $ward->ward_id)
            ->whereNull('in_patient.actual_leave')
            ->count();
        
        $availableCount = $totalBeds - $occupiedCount;

        $stats = [
            'all' => $totalBeds,
            'available' => $availableCount,
            'occupied' => $occupiedCount,
            'maintenance' => 0,
            'reserved' => 0,
        ];

        $beds = $ward->beds()
            ->with(['currentInpatient.patient'])
            ->get();

        $availableBeds = Bed::where('ward_id', $ward->ward_id)
            ->whereDoesntHave('currentInpatient')
            ->get();

        return view('wards.show', [
            'ward' => $ward,
            'beds' => $beds,
            'stats' => $stats,
            'availableBeds' => $availableBeds
        ]);
    }

    public function storeBed(Request $request, $ward_id)
    {
        $request->validate([
            'bed_number' => 'required|string|max:255',
            'bed_type' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            
            Bed::create([
                'bed_number' => $request->bed_number,
                'ward_id' => $ward_id,
                'bed_type' => $request->bed_type ?? 'Standard',
                'is_available' => true,
                'maintenance_status' => 'operational'
            ]);
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Bed created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateBedStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $bed = Bed::findOrFail($id);
            $bed->update([
                'is_available' => ($request->status === 'available'),
                'maintenance_status' => 'operational'
            ]);
            
            DB::commit();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateBed(Request $request, $id)
    {
        try {
            $bed = Bed::findOrFail($id);

            if ($request->action === 'discharge') {
                $inpatient = DB::table('in_patient')
                    ->where('bed_id', $id)
                    ->whereNull('actual_leave')
                    ->first();

                if (!$inpatient) {
                    return response()->json(['success' => false, 'message' => 'No active patient found.'], 404);
                }

                DB::select('SELECT discharge_patient(?::INTEGER)', [(int) $inpatient->inpatient_id]);
                
                return response()->json(['success' => true, 'message' => 'Discharged successfully.']);
            }

            if ($request->action === 'assign') {
                $request->validate([
                    'patient_id' => 'required|exists:patient,patient_id',
                    'diagnosis' => 'nullable|string',
                    'condition' => 'nullable|string'
                ]);

                // Check if bed is available
                if (!$bed->is_available) {
                    return response()->json(['success' => false, 'message' => 'Bed is currently occupied or unavailable.'], 422);
                }

                // Check for active patient on this bed
                $hasActivePatient = DB::table('in_patient')
                    ->where('bed_id', $bed->bed_id)
                    ->whereNull('actual_leave')
                    ->exists();

                if ($hasActivePatient) {
                    return response()->json(['success' => false, 'message' => 'Bed is already occupied.'], 422);
                }

                // Call the Supabase function
                DB::select('SELECT admit_patient(?::INTEGER, ?::INTEGER, ?::VARCHAR, ?::VARCHAR)', [
                    (int) $request->patient_id,
                    (int) $bed->bed_id,
                    (string) ($request->diagnosis ?? 'Standard Care'),
                    (string) ($request->condition ?? 'Stable')
                ]);

                // If we got here without exception, it succeeded
                return response()->json(['success' => true, 'message' => 'Assigned successfully.']);
            }

            return response()->json(['success' => false, 'message' => 'Invalid action.'], 400);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation error: ' . json_encode($e->errors())], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update bed details (number and type)
     */
    public function updateBedDetails(Request $request, $id)
    {
        try {
            $request->validate([
                'bed_number' => 'required|string|max:255',
                'bed_type' => 'nullable|string'
            ]);

            $bed = Bed::findOrFail($id);
            $bed->update([
                'bed_number' => $request->bed_number,
                'bed_type' => $request->bed_type,
                'updated_at' => now()
            ]);
            
            return response()->json(['success' => true, 'message' => 'Bed updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

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

        Ward::create($validated);
        
        return redirect()->route('wards.management')->with('success', 'Ward created successfully');
    }
}