<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeProfileController;
use App\Http\Controllers\Api\SalaryAdvanceController;

// 🔐 Auth employé
Route::prefix('auth')->group(function () {
    Route::post('login-employee', [AuthController::class,'loginEmployee']);
});

// Test API
Route::get('ping', fn() => response()->json(['ok' => true]));

// ✅ Routes protégées employés (mobile)
Route::middleware('auth:employee-token')->group(function () {
    // Profil employé
    Route::get('employee/profile', [EmployeeProfileController::class,'show']);
    Route::put('employee/profile', [EmployeeProfileController::class,'update']);

    // ⚡ Nouveau endpoint : définir mot de passe la première fois
    Route::post('employee/set-password', [EmployeeProfileController::class,'setPassword']);

    // Avances sur salaire
    Route::post('salary-advances', [SalaryAdvanceController::class,'store']);
    Route::get('salary-advances', [SalaryAdvanceController::class,'index']);
});
