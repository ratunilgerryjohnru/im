<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Inpatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BedController extends Controller
{
    /**
     * Store a newly created bed in storage.
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
                'message' => 'Bed added and ward capacity updated.',
                'data' => $bed
            ], 201);

        } catch (\Exception $e) {
            Log::error("Error adding bed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Bed Status Updates and Patient Assignments.
     * This prevents the "Occupied with no patient" bug.
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

                // EXCEPTION GUARD: If setting to occupied but no patient exists
                if ($newStatus === 'occupied' && !$bed->currentInpatient) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Exception: Cannot set bed to Occupied without an active patient record. Please use "Assign Patient" instead.'
                    ], 422);
                }

                $bed->update(['is_available' => ($newStatus === 'available')]);
            }

            // 2. ACTION: ASSIGNING A NEW PATIENT
            if ($action === 'assign') {
                $request->validate(['patient_id' => 'required|exists:patients,patient_id']);

                // Create the Inpatient record
                Inpatient::create([
                    'patient_id' => $request->patient_id,
                    'bed_id' => $bed->bed_id,
                    'admission_date' => now(),
                    'status' => 'Admitted'
                ]);

                // Automatically flip bed to occupied
                $bed->update(['is_available' => false]);
            }

            // 3. ACTION: DISCHARGING A PATIENT
            if ($action === 'discharge') {
                if ($bed->currentInpatient) {
                    $bed->currentInpatient->update([
                        'discharge_date' => now(),
                        'status' => 'Discharged'
                    ]);
                }
                
                // Automatically flip bed to available
                $bed->update(['is_available' => true]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Bed updated successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Bed Update Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}