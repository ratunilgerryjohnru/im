<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BedController extends Controller
{
    /**
     * Store a newly created bed in storage.
     * Triggers handle ward available_beds sync automatically
     */
    public function store(Request $request, $ward_id)
    {
        $validated = $request->validate([
            'bed_name' => 'required|string|max:255',
        ]);

        try {
            $bed = Bed::create([
                'bed_name' => $validated['bed_name'],
                'ward_id' => $ward_id,
                'bed_type' => 'Standard', 
                'is_available' => true,
                'maintenance_status' => 'operational'
            ]);

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
     * Handle Bed Status Updates and Patient Assignments.
     * Using Supabase procedures
     */
    public function update(Request $request, $id)
    {
        $bed = Bed::with('currentInpatient')->findOrFail($id);
        $action = $request->input('action');

        try {
            DB::beginTransaction();

            // 1. ACTION: MANUALLY UPDATING STATUS DROPDOWN
            if ($request->has('status')) {
                $newStatus = $request->input('status');

                // Validation: Cannot set to occupied without a patient
                if ($newStatus === 'occupied' && !$bed->currentInpatient) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot set bed to Occupied without an active patient record. Please use "Assign Patient" instead.'
                    ], 422);
                }

                $bed->update(['is_available' => ($newStatus === 'available')]);
                // Triggers handle ward sync automatically
            }

            // 2. ACTION: ASSIGNING A NEW PATIENT - Use procedure
            if ($action === 'assign') {
                $request->validate(['patient_id' => 'required|exists:patient,patient_id']);
                
                DB::statement('CALL admit_patient(?, ?, ?, ?)', [
                    $request->patient_id,
                    $bed->bed_id,
                    $request->diagnosis ?? 'Standard Care',
                    $request->condition ?? 'Stable'
                ]);
            }

            // 3. ACTION: DISCHARGING A PATIENT - Use procedure
            if ($action === 'discharge') {
                if ($bed->currentInpatient) {
                    DB::statement('CALL discharge_patient(?)', [$bed->currentInpatient->inpatient_id]);
                } else {
                    $bed->update(['is_available' => true]);
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