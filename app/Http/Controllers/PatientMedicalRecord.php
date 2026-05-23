<?php

namespace App\Http\Controllers;

use App\Models\PatientMedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index()
    {
        return view('medical-records');
    }

    public function getRecords()
    {
        try {
            $records = PatientMedicalRecord::with('patient')
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
                'record_type' => 'required|string',
                'recorded_by' => 'required|string',
                'description' => 'required|string',
            ]);

            $record = PatientMedicalRecord::create([
                'patient_id' => $validated['patient_id'],
                'diagnosis' => $validated['record_type'] === 'diagnosis' ? $validated['description'] : null,
                'chronic_conditions' => $validated['record_type'] === 'treatment' ? $validated['description'] : null,
                'created_date' => $validated['record_date'],
            ]);

            return response()->json(['success' => true, 'message' => 'Medical record added', 'record' => $record]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $record = PatientMedicalRecord::findOrFail($id);
            $record->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}