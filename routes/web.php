<?php

use Illuminate\Support\Facades\Route;

// ---- Middlewares ----
use App\Http\Middleware\EnsureCompanyActive;
use App\Http\Middleware\EnsureAdminActive;

// ---- Contrôleurs (Entreprise) ----
use App\Http\Controllers\Web\CompanyAuthController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\CompanyAdvanceController;
use App\Http\Controllers\Web\CompanyAdvanceExportController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\CompanyProfileController;
// ---- Contrôleurs (Admin) ----
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CompanyAdminController;
use App\Http\Controllers\Admin\AdminUserController;

// ===============================
// Accueil (optionnel)

// Route::get('/', fn () => view('welcome'));
Route::view('/', 'maintenance'); // << page maintenance


// ===============================
// 🚀 Auth Entreprise (public)
Route::middleware('guest')->group(function () {
    Route::get('/register', [CompanyAuthController::class, 'showRegister'])->name('company.register');
    Route::post('/register', [CompanyAuthController::class, 'register']);

    Route::get('/login', [CompanyAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CompanyAuthController::class, 'login'])->name('company.login');
});

// Logout Entreprise
Route::post('/logout', [CompanyAuthController::class, 'logout'])
    ->middleware('auth:web')
    ->name('company.logout');

// ===============================
// ✅ Espace Entreprise
Route::middleware(['auth:web', EnsureCompanyActive::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('company.dashboard');

    // Employés
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::get('employees-import', [EmployeeController::class, 'showImport'])->name('employees.import.form');
    Route::post('employees-import', [EmployeeController::class, 'import'])->name('employees.import');

    // Avances
    Route::get('/advances', [CompanyAdvanceController::class, 'index'])->name('advances.index');
    Route::get('/advances/{advance}', [CompanyAdvanceController::class, 'show'])->whereNumber('advance')->name('advances.show');
    Route::patch('/advances/{advance}/payout/success', [CompanyAdvanceController::class, 'markPayoutSuccess'])->whereNumber('advance')->name('advances.payout.success');
    Route::patch('/advances/{advance}/payout/failed', [CompanyAdvanceController::class, 'markPayoutFailed'])->whereNumber('advance')->name('advances.payout.failed');

    // Export Sage
    Route::get('/advances/export/sage', [CompanyAdvanceExportController::class, 'exportSage'])->name('advances.export.sage');

    Route::get('/profile',             [CompanyProfileController::class,'show'])->name('company.profile.show');
    Route::put('/profile',             [CompanyProfileController::class,'update'])->name('company.profile.update');
    Route::put('/profile/logo',        [CompanyProfileController::class,'updateLogo'])->name('company.profile.logo');
    Route::put('/profile/password',    [CompanyProfileController::class,'updatePassword'])->name('company.profile.password');
});

// ===============================
// 🔐 Auth Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('doLogin');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->middleware('auth:admin')
        ->name('logout');

    // Zone protégée Admin
    Route::middleware(['auth:admin', EnsureAdminActive::class])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Entreprises
        Route::get('/companies', [CompanyAdminController::class, 'index'])->name('companies.index');
        Route::patch('/companies/{company}/toggle', [CompanyAdminController::class, 'toggle'])->name('companies.toggle');
        Route::post('/companies/{company}/impersonate', [CompanyAdminController::class, 'impersonate'])->name('companies.impersonate');
        Route::post('/stop-impersonate', [CompanyAdminController::class, 'stopImpersonate'])->name('stopImpersonate');

        // Admins
        Route::get('/users',              [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create',       [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users',             [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{admin}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{admin}',      [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{admin}',   [AdminUserController::class, 'destroy'])->name('users.destroy');

        // ✅ Toggle actif/inactif d’un admin
        Route::patch('/users/{admin}/toggle', [AdminUserController::class, 'toggle'])->name('users.toggle');
    });
});
