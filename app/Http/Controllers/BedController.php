<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BedController extends Controller
{
    /**
     * Store a newly created bed
     */
    public function store(Request $request, $ward_id)
    {
        $validated = $request->validate([
            'bed_number' => 'required|string|max:255',
            'bed_type' => 'nullable|string',
        ]);

        try {
            $bed = Bed::create([
                'bed_number' => $validated['bed_number'],
                'ward_id' => $ward_id,
                'bed_type' => $validated['bed_type'] ?? 'Standard',
                'is_available' => true,
                'maintenance_status' => 'operational',
                'is_active' => true
            ]);

            // Refresh ward available beds count
            $bed->ward->refreshAvailableBedsCount();

            return response()->json([
                'success' => true,
                'message' => 'Bed added successfully.',
                'data' => $bed
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update bed status or assign/discharge patient
     */
    public function update(Request $request, $id)
    {
        $bed = Bed::with('currentInpatient')->findOrFail($id);
        $action = $request->input('action');

        try {
            DB::beginTransaction();

            // Update status only
            if ($request->has('status')) {
                $newStatus = $request->input('status');
                
                if ($newStatus === 'occupied' && !$bed->currentInpatient) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot set bed to Occupied without an active patient. Use "Assign Patient" instead.'
                    ], 422);
                }
                
                $bed->update(['is_available' => ($newStatus === 'available')]);
                $bed->ward->refreshAvailableBedsCount();
            }

            // Assign patient - use Supabase function
            if ($action === 'assign') {
                $request->validate(['patient_id' => 'required|exists:patient,patient_id']);
                
                if ($bed->currentInpatient) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bed already occupied'
                    ], 422);
                }
                
                $result = DB::select('SELECT admit_patient(?::INTEGER, ?::INTEGER, ?::VARCHAR, ?::VARCHAR)', [
                    $request->patient_id,
                    $bed->bed_id,
                    $request->diagnosis ?? 'Standard Care',
                    $request->condition ?? 'Stable'
                ]);
            }

            // Discharge patient - use Supabase function
            if ($action === 'discharge') {
                if ($bed->currentInpatient) {
                    $result = DB::select('SELECT discharge_patient(?::INTEGER)', [
                        $bed->currentInpatient->inpatient_id
                    ]);
                } else {
                    $bed->update(['is_available' => true]);
                    $bed->ward->refreshAvailableBedsCount();
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Operation completed successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}