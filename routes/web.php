<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EngineerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:engineer'])->group(function () {
    Route::get('/engineer/dashboard', [EngineerController::class, 'index'])
        ->name('engineer.dashboard');

    Route::post('/engineer/calculate', [EngineerController::class, 'calculate'])
        ->name('engineer.calculate');

    Route::post('/engineer/preview', [EngineerController::class, 'preview'])
        ->name('engineer.preview');

    Route::delete('/engineer/projects/{project}', [EngineerController::class, 'destroy'])
        ->name('engineer.projects.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])
        ->name('admin.dashboard');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'index'])
        ->name('user.dashboard');

    Route::post('/user/calculate', [UserController::class, 'calculate'])
        ->name('user.calculate');

    Route::post('/user/report', [UserController::class, 'downloadPDF'])
        ->name('user.report');
});

Route::get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'engineer' => redirect()->route('engineer.dashboard'),
        default => redirect()->route('user.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:engineer,admin'])->group(function () {
    Route::get('/projects/history', [ProjectController::class, 'index'])->name('projects.history');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/report', [ReportController::class, 'project'])->name('projects.report');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/report', [ReportController::class, 'generate']);

require __DIR__.'/auth.php';
