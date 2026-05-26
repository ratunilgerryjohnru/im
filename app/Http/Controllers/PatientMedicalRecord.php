<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientMedicalRecordController extends Controller
{
    public function index()
    {
        return view('medical-records');
    }

    public function getRecords()
    {
        try {
            $records = MedicalRecord::with('patient')
                ->orderBy('created_date', 'desc')
                ->get();
            return response()->json($records);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'records' => []], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|exists:patient,patient_id',
                'diagnosis' => 'nullable|string',
                'blood_type' => 'nullable|string',
                'allergies' => 'nullable|string',
                'chronic_conditions' => 'nullable|string',
            ]);

            // Generate unique record_id
            $maxId = DB::table('patient_medical_record')->max('record_id') ?? 0;
            $newId = $maxId + 1;

            $record = MedicalRecord::create([
                'record_id' => $newId,
                'patient_id' => $validated['patient_id'],
                'diagnosis' => $validated['diagnosis'] ?? null,
                'blood_type' => $validated['blood_type'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'chronic_conditions' => $validated['chronic_conditions'] ?? null,
                'created_date' => now()->toDateString(),
            ]);

            return response()->json(['success' => true, 'message' => 'Medical record added', 'record' => $record]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $record = MedicalRecord::where('record_id', $id)->first();
            if ($record) {
                $record->delete();
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}