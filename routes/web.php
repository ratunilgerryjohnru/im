<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\WardManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    
    // Ward & Bed Management page (combines overview + admission)
    Route::get('/ward-management', [WardManagementController::class, 'index'])->name('wards.management');
    
    /**
     * Ward & Bed Management
     */
    Route::prefix('wards')->group(function () {
        Route::get('/', [WardController::class, 'index'])->name('wards.index');
        Route::get('/create', [WardController::class, 'create'])->name('wards.create');
        Route::post('/', [WardController::class, 'store'])->name('wards.store');
        Route::get('/{ward}/edit', [WardController::class, 'edit'])->name('wards.edit');
        Route::put('/{ward}', [WardController::class, 'update'])->name('wards.update');
        Route::delete('/{ward}', [WardController::class, 'destroy'])->name('wards.destroy');
        Route::get('/{ward}', [WardController::class, 'show'])->name('wards.show');
        Route::post('/{ward}/beds', [WardController::class, 'storeBed'])->name('wards.beds.store');
    });

    /**
     * Bed Updates (API/Axios)
     */
    Route::post('/beds/{id}/update', [WardController::class, 'updateBed'])->name('beds.update');
    Route::post('/beds/{id}/status', [WardController::class, 'updateBedStatus'])->name('beds.status');
    Route::delete('/beds/{id}', [WardController::class, 'destroyBed'])->name('beds.destroy');
    
    // NEW ROUTE: Update bed details (number and type)
    Route::put('/beds/{id}', [WardController::class, 'updateBedDetails'])->name('beds.update-details');

    /**
     * Patient Management
     */
    Route::post('/patients/admit', [PatientController::class, 'admit'])->name('patients.admit');
    Route::post('/patients/admit-existing', [PatientController::class, 'admitExisting'])->name('patients.admit-existing');
    Route::post('/patients/{id}/update', [PatientController::class, 'update'])->name('patients.update');
    
    // NEW ROUTE: Update only clinical information (diagnosis & condition)
    Route::post('/patients/{id}/update-clinical', [PatientController::class, 'updateClinical'])->name('patients.update-clinical');
    
    // API helper to get ward_id from bed_id
    Route::get('/api/bed-ward/{bed_id}', function($bed_id) {
        $bed = App\Models\Bed::find($bed_id);
        return response()->json(['ward_id' => $bed->ward_id ?? null]);
    });

    /**
     * Profile Management
     */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';