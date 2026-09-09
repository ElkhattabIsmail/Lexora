<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AudienceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Ressources principales — Avocat & Administrateur
Route::middleware(['auth', 'verified', 'role:Avocat,Administrateur'])->group(function () {
    Route::resource('clients', ClientController::class);
    Route::resource('dossiers', DossierController::class);
    Route::resource('dossiers.audiences', AudienceController::class)
        ->except(['index', 'show']);
    Route::resource('dossiers.documents', DocumentController::class)
        ->only(['store', 'destroy']);
    Route::resource('factures', FactureController::class);
    Route::resource('factures.paiements', PaiementController::class)
        ->only(['store', 'destroy']);
});

// Administration — Gestion des utilisateurs et des rôles (US 11)
Route::middleware(['auth', 'role:Administrateur'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.update-role');
});

require __DIR__.'/auth.php';
