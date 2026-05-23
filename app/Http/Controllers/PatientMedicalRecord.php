<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;  // Model name is MedicalRecord
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                'record_date' => 'required|date',
                'record_type' => 'required|string|in:diagnosis,treatment,lab_result,allergy,chronic_condition',
                'description' => 'required|string',
            ]);

            $record = MedicalRecord::create([
                'patient_id' => $validated['patient_id'],
                'diagnosis' => $validated['record_type'] === 'diagnosis' ? $validated['description'] : null,
                'chronic_conditions' => $validated['record_type'] === 'chronic_condition' ? $validated['description'] : null,
                'allergies' => $validated['record_type'] === 'allergy' ? $validated['description'] : null,
                'created_date' => $validated['record_date'],
                'record_type' => $validated['record_type'],
                'description' => $validated['description'],
                'recorded_by' => Auth::user() ? Auth::user()->name : 'System',
                'updated_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Medical record added', 'record' => $record]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $record = MedicalRecord::findOrFail($id);
            $record->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}