<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/clients', function () {
    return view('clients.index');
})->middleware(['auth', 'verified'])->name('clients.index');

Route::get('/clients/create', function () {
    return view('clients.create');
})->middleware(['auth', 'verified'])->name('clients.create');

Route::get('/clients/show', function () {
    return view('clients.show');
})->middleware(['auth', 'verified'])->name('clients.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
