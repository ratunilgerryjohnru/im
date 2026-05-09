<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Bed;
use App\Models\Inpatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * Admit a new patient and assign to bed
     */
    public function admit(Request $request)
    {
        try {
            $request->validate([
                'patient_name' => 'required|string|max:255',
                'age' => 'required|integer|min:0|max:150',
                'diagnosis' => 'required|string',
                'bed_id' => 'required|exists:bed,bed_id',
                'ward_id' => 'required|exists:ward,ward_id'
            ]);

            DB::beginTransaction();
            
            $bed = Bed::findOrFail($request->bed_id);
            if (!$bed->is_available) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Selected bed is not available'
                ], 422);
            }

            $dateOfBirth = Carbon::now()->subYears($request->age)->toDateString();

            $patient = new Patient();
            $patient->patient_name = $request->patient_name;
            $patient->date_of_birth = $dateOfBirth;
            $patient->save();

            $inpatient = new Inpatient();
            $inpatient->patient_id = $patient->patient_id;
            $inpatient->ward_id = $request->ward_id;
            $inpatient->bed_id = $request->bed_id;
            $inpatient->admission_date = Carbon::now();
            $inpatient->primary_diagnosis = $request->diagnosis;
            $inpatient->admission_type = 'Emergency';
            $inpatient->save();

            $bed->is_available = false;
            $bed->save();

            $this->syncWardAvailableBedsCount($request->ward_id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Patient admitted successfully',
                'patient_id' => $patient->patient_id,
                'inpatient_id' => $inpatient->inpatient_id
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
     * Update patient information
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'patient_name' => 'required|string|max:255',
                'diagnosis' => 'nullable|string',
                'condition' => 'nullable|string'
            ]);

            DB::beginTransaction();

            $patient = Patient::findOrFail($id);
            $patient->patient_name = $request->patient_name;
            $patient->save();

            // Update the inpatient record for this patient (current admission)
            $inpatient = Inpatient::where('patient_id', $id)
                ->whereNull('discharge_date')
                ->first();
            
            if ($inpatient) {
                if ($request->has('diagnosis')) {
                    $inpatient->primary_diagnosis = $request->diagnosis;
                }
                if ($request->has('condition')) {
                    $inpatient->condition = $request->condition;
                }
                $inpatient->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Patient information updated successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to sync ward's available beds count
     */
    private function syncWardAvailableBedsCount($wardId)
    {
        $availableCount = Bed::where('ward_id', $wardId)
            ->where('is_available', true)
            ->count();
        
        \App\Models\Ward::where('ward_id', $wardId)->update(['available_beds' => $availableCount]);
    }
}