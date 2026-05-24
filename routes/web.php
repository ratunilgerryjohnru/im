<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\WardManagementController;
use App\Http\Controllers\PatientMedicalRecordController;
use App\Http\Controllers\StatsController;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// ============ DEBUG ROUTES (Remove after fixing) ============
Route::get('/debug-db', function() {
    try {
        DB::connection()->getPdo();
        return "✅ Database connection successful!";
    } catch (\Exception $e) {
        return "❌ Database connection failed: " . $e->getMessage();
    }
});

Route::get('/debug-patient-create', function() {
    try {
        $patient = Patient::create([
            'first_name' => 'Debug',
            'last_name' => 'Test',
            'phone' => '1234567890',
            'date_registered' => now()->toDateString()
        ]);
        return "✅ Patient created successfully! ID: " . $patient->patient_id;
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

Route::get('/debug-patient-list', function() {
    try {
        $patients = Patient::all();
        return response()->json($patients);
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

Route::get('/debug-auth-check', function() {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->user() ? auth()->user()->name : null
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
        
        // Admissions endpoint
        Route::post('/admissions', [PatientController::class, 'admitExisting'])->name('admissions.store');
        
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