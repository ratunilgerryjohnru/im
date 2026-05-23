<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Bed;
use App\Models\InPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * Admit a new patient and assign to bed using Supabase procedure
     */
    public function admit(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'age' => 'required|integer|min:0|max:150',
                'diagnosis' => 'required|string',
                'bed_id' => 'required|exists:bed,bed_id',
                'ward_id' => 'required|exists:ward,ward_id'
            ]);

            DB::beginTransaction();
            
            // Check bed availability using database function
            $bedAvailable = DB::select('SELECT is_available FROM bed WHERE bed_id = ?', [$request->bed_id]);
            if (empty($bedAvailable) || !$bedAvailable[0]->is_available) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Selected bed is not available'
                ], 422);
            }

            $dateOfBirth = Carbon::now()->subYears($request->age)->toDateString();

            // Create patient (triggers handle timestamps)
            $patient = DB::table('patient')->insertGetId([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'dob' => $dateOfBirth,
                'date_registered' => Carbon::now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // FIXED: Use SELECT with type casting instead of CALL
            DB::select('SELECT admit_patient(?::INTEGER, ?::INTEGER, ?::VARCHAR, ?::VARCHAR)', [
                (int) $patient,
                (int) $request->bed_id,
                (string) $request->diagnosis,
                (string) ($request->condition ?? 'Stable')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Patient admitted successfully',
                'patient_id' => $patient
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . json_encode($e->errors())
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admit an existing patient to a bed using Supabase procedure
     */
    public function admitExisting(Request $request)
    {
        try {
            $request->validate([
                'patient_id' => 'required|exists:patient,patient_id',
                'diagnosis' => 'required|string',
                'condition' => 'nullable|string',
                'bed_id' => 'required|exists:bed,bed_id',
                'ward_id' => 'required|exists:ward,ward_id'
            ]);

            // FIXED: Use SELECT with type casting instead of CALL
            DB::select('SELECT admit_patient(?::INTEGER, ?::INTEGER, ?::VARCHAR, ?::VARCHAR)', [
                (int) $request->patient_id,
                (int) $request->bed_id,
                (string) $request->diagnosis,
                (string) ($request->condition ?? 'Stable')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Patient admitted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update clinical information ONLY (diagnosis and condition)
     */
    public function updateClinical(Request $request, $id)
    {
        try {
            $request->validate([
                'diagnosis' => 'required|string|max:255',
                'condition' => 'nullable|string|max:100'
            ]);

            DB::beginTransaction();

            $updated = DB::table('in_patient')
                ->where('patient_id', $id)
                ->whereNull('actual_leave')
                ->update([
                    'primary_diagnosis' => $request->diagnosis,
                    'condition' => $request->condition ?? 'Stable',
                    'updated_at' => now()
                ]);

            if ($updated === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No active inpatient record found for this patient'
                ], 404);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Clinical information updated successfully'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . json_encode($e->errors())
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @deprecated Use updateClinical() instead
     */
    public function update(Request $request, $id)
    {
        $request->merge([
            'diagnosis' => $request->diagnosis,
            'condition' => $request->condition
        ]);
        
        return $this->updateClinical($request, $id);
    }
}