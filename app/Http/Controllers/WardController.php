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
        return view('wards.index');
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

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // Find the ward by ID instead of using route model binding
            $ward = Ward::findOrFail($id);
            
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
            
            // Delete beds
            DB::table('bed')->where('ward_id', $ward->ward_id)->delete();
            
            // Delete the ward
            $ward->delete();
            
            DB::commit();
            
            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Ward deleted successfully']);
            }
            
            return redirect()->route('wards.management')->with('success', 'Ward deleted successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            
            return redirect()->route('wards.management')->with('error', 'Error deleting ward: ' . $e->getMessage());
        }
    }

    public function destroyBed($id)
    {
        try {
            DB::beginTransaction();
            
            $activeInpatient = DB::table('in_patient')
                ->where('bed_id', $id)
                ->whereNull('actual_leave')
                ->first();
            
            if ($activeInpatient) {
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

    public function show($id)
    {
        return view('wards.show', ['wardId' => (int)$id]);
    }

    public function getBedsData($id)
    {
        $ward = Ward::findOrFail($id);
        $beds = Bed::where('ward_id', $id)->get();
        
        $bedsWithPatients = [];
        $occupiedCount = 0;
        
        foreach ($beds as $bed) {
            $inpatient = InPatient::where('bed_id', $bed->bed_id)
                ->whereNull('actual_leave')
                ->with('patient')
                ->first();
            
            if ($inpatient) {
                $occupiedCount++;
                $bedsWithPatients[] = [
                    'bed_id' => $bed->bed_id,
                    'bed_number' => $bed->bed_number,
                    'bed_type' => $bed->bed_type,
                    'current_inpatient' => [
                        'inpatient_id' => $inpatient->inpatient_id,
                        'patient_id' => $inpatient->patient_id,
                        'primary_diagnosis' => $inpatient->primary_diagnosis,
                        'condition' => $inpatient->condition,
                        'date_admitted' => $inpatient->date_admitted,
                        'patient' => $inpatient->patient ? [
                            'patient_id' => $inpatient->patient->patient_id,
                            'first_name' => $inpatient->patient->first_name,
                            'last_name' => $inpatient->patient->last_name
                        ] : null
                    ]
                ];
            } else {
                $bedsWithPatients[] = [
                    'bed_id' => $bed->bed_id,
                    'bed_number' => $bed->bed_number,
                    'bed_type' => $bed->bed_type,
                    'current_inpatient' => null
                ];
            }
        }
        
        return response()->json([
            'ward_name' => $ward->ward_name,
            'total_beds' => $ward->total_beds,
            'beds' => $bedsWithPatients,
            'stats' => [
                'all' => $beds->count(),
                'occupied' => $occupiedCount,
                'available' => $beds->count() - $occupiedCount
            ]
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

                if (!$bed->is_available) {
                    return response()->json(['success' => false, 'message' => 'Bed is currently occupied or unavailable.'], 422);
                }

                $hasActivePatient = DB::table('in_patient')
                    ->where('bed_id', $bed->bed_id)
                    ->whereNull('actual_leave')
                    ->exists();

                if ($hasActivePatient) {
                    return response()->json(['success' => false, 'message' => 'Bed is already occupied.'], 422);
                }

                DB::select('SELECT admit_patient(?::INTEGER, ?::INTEGER, ?::VARCHAR, ?::VARCHAR)', [
                    (int) $request->patient_id,
                    (int) $bed->bed_id,
                    (string) ($request->diagnosis ?? 'Standard Care'),
                    (string) ($request->condition ?? 'Stable')
                ]);

                return response()->json(['success' => true, 'message' => 'Assigned successfully.']);
            }

            return response()->json(['success' => false, 'message' => 'Invalid action.'], 400);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation error: ' . json_encode($e->errors())], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

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