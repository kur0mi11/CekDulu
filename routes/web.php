<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerifierController;
use App\Http\Controllers\AdminController;

// rute public
Route::get('/', [PageController::class, 'index']);

//rute guest kalau blm login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Rute Register Baru
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// rute terauntentikasi (wajib login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // general dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // user workspace
    Route::middleware('role:User')->group(function () {
        Route::resource('reports', ReportController::class); 
    });

    // verifier workspace
    Route::middleware('role:Verifier')->prefix('verifier')->name('verifier.')->group(function () {
        Route::get('/antrean', [VerifierController::class, 'index'])->name('index');
        Route::get('/laporan/{id}', [VerifierController::class, 'show'])->name('reports.show');
        Route::patch('/laporan/{id}/status', [VerifierController::class, 'updateStatus'])->name('reports.update');
        
        // Rute streaming file privat
        Route::get('/laporan/{id}/evidence', [VerifierController::class, 'showEvidence'])->name('reports.evidence');
    });

    // admin workspace
    Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/antrean', [AdminController::class, 'index'])->name('index');
        Route::patch('/laporan/{id}/status', [AdminController::class, 'updateStatus'])->name('reports.update');
    });
});