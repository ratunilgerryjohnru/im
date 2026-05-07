<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    
    // Ward & Bed Management Routes
    Route::get('/wards', [WardController::class, 'index'])->name('wards.index');
    Route::post('/wards', [WardController::class, 'store'])->name('wards.store');
    
    /**
     * Dynamic Route for Ward Details
     * This allows clicking 'View Details' to show a specific ward's beds
     */
    Route::get('/wards/{ward}', [WardController::class, 'show'])->name('wards.show');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';