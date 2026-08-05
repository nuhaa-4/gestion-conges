<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [LeaveController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/manager/dashboard', [LeaveController::class, 'managerDashboard'])->middleware(['auth', 'verified'])->name('manager.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::post('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');

    // Gestion des salariés
    Route::get('/manager/employees', [EmployeeController::class, 'index'])->name('manager.employees.index');
    Route::get('/manager/employees/{user}/edit', [EmployeeController::class, 'edit'])->name('manager.employees.edit');
    Route::put('/manager/employees/{user}', [EmployeeController::class, 'update'])->name('manager.employees.update');
});

require __DIR__.'/auth.php';