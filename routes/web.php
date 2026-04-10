<?php

use App\Http\Controllers\EngineerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:engineer'])->group(function() {
    Route::get('/engineer/dashboard', [EngineerController::class, 'index'])
    ->name('engineer.dashboard');
});

Route::middleware(['auth', 'role:user'])->group(function() {
    Route::get('/user/dashboard', [UserController::class, 'index'])
    ->name('user.dashboard');
});

Route::get('/dashboard', function() {
    if(auth()->user()->role === 'engineer') {
        return redirect()->route('engineer.dashboard');
    }
    return redirect()->route('user.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/user/calculate', [UserController::class, 'calculate'])
    ->name('user.calculate')
    ->middleware(['auth', 'role:user']);

Route::post('/user/report', [UserController::class, 'downloadPDF'])
->name('user.report');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
