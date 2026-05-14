<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

// ============ PATIENT MANAGEMENT MODULE ============
Route::prefix('patients')->name('patients.')->group(function () {
    // View
    Route::get('/', [PatientController::class, 'index'])->name('index');
    
    // API endpoints (return JSON)
    Route::get('/list', [PatientController::class, 'getPatients'])->name('list');
    Route::get('/{id}', [PatientController::class, 'show'])->name('show');
    Route::post('/', [PatientController::class, 'store'])->name('store');
    Route::put('/{id}', [PatientController::class, 'update'])->name('update');
    Route::delete('/{id}', [PatientController::class, 'destroy'])->name('destroy');
    
    // Patient status
    Route::get('/{id}/status', [PatientController::class, 'getStatus'])->name('status');
});

// ============ ADMISSION MODULE ============
Route::prefix('admissions')->name('admissions.')->group(function () {
    Route::post('/', [AdmissionController::class, 'admit'])->name('store');
    Route::put('/{inpatientId}/discharge', [AdmissionController::class, 'discharge'])->name('discharge');
});

// ============ BEDS & WARDS MODULE ============
Route::prefix('beds')->name('beds.')->group(function () {
    Route::get('/available/{wardId?}', [BedController::class, 'getAvailableBeds'])->name('available');
});

Route::prefix('wards')->name('wards.')->group(function () {
    Route::get('/stats', [BedController::class, 'getAllWardsStats'])->name('stats');
    Route::get('/{wardId}/stats', [BedController::class, 'getWardStats'])->name('stats.single');
});

Route::get('/patients/{patientId}/bed', [BedController::class, 'getPatientCurrentBed'])->name('patients.bed');

// ============ DASHBOARD STATS ============
Route::prefix('stats')->name('stats.')->group(function () {
    Route::get('/dashboard', [StatsController::class, 'dashboard'])->name('dashboard');
    Route::get('/patients/count', [StatsController::class, 'getTotalPatients'])->name('patients.count');
    Route::get('/admissions/active', [StatsController::class, 'getActiveAdmissions'])->name('admissions.active');
    Route::get('/beds/occupied', [StatsController::class, 'getOccupiedBeds'])->name('beds.occupied');
    Route::get('/medical-records/count', [StatsController::class, 'getMedicalRecordsCount'])->name('medical-records.count');
});

// ============ AUTHENTICATION ROUTES (Keep existing) ============
Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';