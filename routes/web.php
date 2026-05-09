<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Patient Management Routes (protected by auth)
Route::middleware(['auth', 'verified'])->group(function () {
    // Main view
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    
    // Patient CRUD operations
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/list', [PatientController::class, 'getPatients'])->name('patients.list');
    Route::put('/patients/{id}/toggle-admission', [PatientController::class, 'toggleAdmission']);
    Route::put('/patients/{id}/toggle-bed', [PatientController::class, 'toggleBed']);
    Route::delete('/patients/{id}', [PatientController::class, 'destroy']);
    
    // Medical Records operations
    Route::post('/medical-records', [PatientController::class, 'addMedicalRecord'])->name('medical-records.store');
    Route::get('/medical-records', [PatientController::class, 'getMedicalRecords']);
    Route::delete('/medical-records/{id}', [PatientController::class, 'deleteMedicalRecord']);
    
    // Statistics
    Route::get('/stats', [PatientController::class, 'getStats']);
});

require __DIR__.'/auth.php';