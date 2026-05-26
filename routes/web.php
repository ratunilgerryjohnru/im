<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\WardManagementController;
use App\Http\Controllers\PatientMedicalRecordController;
use App\Http\Controllers\StatsController;
use App\Models\Patient;
use App\Models\Ward;
use App\Models\Bed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// ============ DEBUG PATIENT ADMISSION ROUTE ============
Route::get('/debug-patient-admission/{id}', function($id) {
    $patient = App\Models\Patient::find($id);
    $inpatient = App\Models\InPatient::where('patient_id', $id)->whereNull('actual_leave')->first();
    
    return response()->json([
        'patient_exists' => !is_null($patient),
        'patient_id' => $id,
        'patient_name' => $patient ? $patient->first_name . ' ' . $patient->last_name : null,
        'has_active_admission' => !is_null($inpatient),
        'inpatient_data' => $inpatient,
        'message' => $inpatient ? '✅ Patient is admitted. Clinical update will work.' : '❌ Patient is NOT admitted. Please admit first.'
    ]);
});

// ============ DEBUG BEDS ROUTE ============
Route::get('/debug-beds/{id}', function($id) {
    try {
        $ward = Ward::findOrFail($id);
        $beds = Bed::where('ward_id', $id)->get();
        
        return response()->json([
            'ward_name' => $ward->ward_name,
            'ward_exists' => true,
            'bed_count' => $beds->count(),
            'beds' => $beds->toArray(),
            'total_beds_column' => $ward->total_beds
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// ============ API STATS ENDPOINT (Works without auth) ============
Route::get('/api/stats/all', function() {
    try {
        return response()->json([
            'total_patients' => DB::table('patient')->count(),
            'active_admissions' => DB::table('in_patient')->whereNull('actual_leave')->count(),
            'occupied_beds' => DB::table('bed')->where('is_available', false)->count(),
            'medical_records' => DB::table('patient_medical_record')->count(),
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// ============ DEBUG ROUTES ============
Route::get('/debug-db', function() {
    try {
        DB::connection()->getPdo();
        return response()->json([
            'success' => true,
            'message' => 'Database connected successfully!',
            'db_host' => env('DB_HOST'),
            'db_database' => env('DB_DATABASE'),
            'db_username' => env('DB_USERNAME'),
            'supabase_url' => env('SUPABASE_URL'),
            'patient_count' => Patient::count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'db_host' => env('DB_HOST'),
            'db_database' => env('DB_DATABASE'),
            'db_username' => env('DB_USERNAME')
        ], 500);
    }
});

Route::get('/debug-supabase', function() {
    try {
        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_KEY');
        
        $ch = curl_init($supabaseUrl . '/rest/v1/patient?limit=1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $supabaseKey,
            'Authorization: Bearer ' . $supabaseKey
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return response()->json([
            'success' => $httpCode === 200,
            'http_code' => $httpCode,
            'supabase_url' => $supabaseUrl,
            'response' => json_decode($response)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::get('/debug-env', function() {
    return response()->json([
        'APP_ENV' => env('APP_ENV'),
        'APP_DEBUG' => env('APP_DEBUG'),
        'DB_HOST' => env('DB_HOST'),
        'DB_DATABASE' => env('DB_DATABASE'),
        'DB_USERNAME' => env('DB_USERNAME'),
        'SUPABASE_URL' => env('SUPABASE_URL'),
        'SUPABASE_KEY' => substr(env('SUPABASE_KEY'), 0, 20) . '...'
    ]);
});

// ============ ROOT ROUTE ============
Route::get('/', function () {
    return view('welcome');
});

// ============ AUTHENTICATED ROUTES ============
Route::middleware(['auth'])->group(function () {
    
    // Dashboard - accessible by BOTH admin AND guest
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile Management - accessible by both
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // SEARCH ROUTES - accessible by BOTH admin AND guest (moved outside admin group)
    Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
    Route::get('/patients/{id}/full-details', [PatientController::class, 'getFullDetails'])->name('patients.full-details');

    // STATISTICS ROUTES - accessible by BOTH admin AND guest (for dashboard stats)
    Route::prefix('stats')->name('stats.')->group(function () {
        Route::get('/total-patients', [StatsController::class, 'getTotalPatients'])->name('total-patients');
        Route::get('/active-admissions', [StatsController::class, 'getActiveAdmissions'])->name('active-admissions');
        Route::get('/occupied-beds', [StatsController::class, 'getOccupiedBeds'])->name('occupied-beds');
        Route::get('/medical-records', [StatsController::class, 'getMedicalRecordsCount'])->name('medical-records');
        Route::get('/all', [StatsController::class, 'getAllStats'])->name('all');
        Route::get('/active-admissions/details', [StatsController::class, 'getActiveAdmissionsDetails'])->name('active-admissions.details');
    });

    // ============ ADMISSIONS ROUTES (FIXED) ============
    // Primary route - POST method
    Route::post('/admissions', [PatientController::class, 'admitExisting'])->name('admissions.store');
    // Alternative route in case of caching issues
    Route::post('/admit-patient', [PatientController::class, 'admitExisting'])->name('admissions.alternative');
    // Debug route to test if routing works
    Route::get('/admissions-test', function() {
        return response()->json(['message' => 'Admissions route is accessible', 'method' => 'GET']);
    });

    // ============ ADMIN ONLY ROUTES ============
    Route::middleware(['role:admin'])->group(function () {
        
        Route::prefix('patients')->name('patients.')->group(function () {
            Route::get('/', [PatientController::class, 'index'])->name('index');
            Route::get('/list', [PatientController::class, 'getPatients'])->name('list');
            Route::get('/{id}', [PatientController::class, 'show'])->name('show');
            Route::post('/', [PatientController::class, 'store'])->name('store');
            Route::put('/{id}', [PatientController::class, 'update'])->name('update');
            Route::delete('/{id}', [PatientController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/status', [PatientController::class, 'getStatus'])->name('status');
            Route::get('/{id}/current-admission', [PatientController::class, 'currentAdmission'])->name('current-admission');
        });

        // Medical Records (write operations - admin only)
        Route::prefix('medical-records')->name('medical-records.')->group(function () {
            Route::get('/', [PatientMedicalRecordController::class, 'index'])->name('index');
            Route::get('/list', [PatientMedicalRecordController::class, 'getRecords'])->name('list');
            Route::post('/', [PatientMedicalRecordController::class, 'store'])->name('store');
            Route::delete('/{id}', [PatientMedicalRecordController::class, 'destroy'])->name('destroy');
        });
        
        // Ward & Bed Management
        Route::get('/ward-management', [WardManagementController::class, 'index'])->name('wards.management');
        
        Route::get('/wards/{id}/beds-data', [WardController::class, 'getBedsData']);
        
        Route::prefix('wards')->name('wards.')->group(function () {
            Route::get('/', [WardController::class, 'index'])->name('index');
            Route::get('/create', [WardController::class, 'create'])->name('create');
            Route::post('/', [WardController::class, 'store'])->name('store');
            Route::get('/{ward}/edit', [WardController::class, 'edit'])->name('edit');
            Route::put('/{ward}', [WardController::class, 'update'])->name('update');
            Route::delete('/{ward}', [WardController::class, 'destroy'])->name('destroy');
            Route::get('/{ward}', [WardController::class, 'show'])->name('show');
            Route::post('/{ward}/beds', [WardController::class, 'storeBed'])->name('beds.store');
        });

        // Bed Updates (API/Axios)
        Route::prefix('beds')->name('beds.')->group(function () {
            Route::post('/{id}/update', [WardController::class, 'updateBed'])->name('update');
            Route::post('/{id}/status', [WardController::class, 'updateBedStatus'])->name('status');
            Route::delete('/{id}', [WardController::class, 'destroyBed'])->name('destroy');
            Route::put('/{id}', [WardController::class, 'updateBedDetails'])->name('update-details');
        });

        // Patient Management - Admission routes
        Route::post('/patients/admit', [PatientController::class, 'admit'])->name('patients.admit');
        Route::post('/patients/admit-existing', [PatientController::class, 'admitExisting'])->name('patients.admit-existing');
        Route::post('/patients/{id}/update-clinical', [PatientController::class, 'updateClinical'])->name('patients.update-clinical');
        Route::put('/inpatients/{inpatientId}/discharge', [PatientController::class, 'discharge'])->name('inpatients.discharge');
        
        // API helper
        Route::get('/api/bed-ward/{bed_id}', function($bed_id) {
            $bed = App\Models\Bed::find($bed_id);
            return response()->json(['ward_id' => $bed->ward_id ?? null]);
        });
    });
});

require __DIR__.'/auth.php';