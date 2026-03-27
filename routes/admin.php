<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\DashboardController;

// 1. Rotas Públicas (NÃO podem ter admin.auth)
Route::middleware(['web'])->group(function () {
    Route::get('/login',  [DashboardController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [DashboardController::class, 'login'])->name('admin.login.post');
});

// 2. Rotas Protegidas (PRECISAM de admin.auth)
Route::middleware(['web', 'admin.auth'])->group(function () {

    // Dashboard - Acessível em admin.nimvo.com.br/admin/
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Tenants - Acessível em admin.nimvo.com.br/admin/tenants/...
    Route::prefix('tenants')->group(function () {
        Route::get('/',         [TenantController::class, 'index'])->name('admin.tenants.index');
        Route::get('/create',   [TenantController::class, 'create'])->name('admin.tenants.create');
        Route::post('/',        [TenantController::class, 'store'])->name('admin.tenants.store');
        Route::get('/{id}',     [TenantController::class, 'show'])->name('admin.tenants.show');
        Route::delete('/{id}',  [TenantController::class, 'destroy'])->name('admin.tenants.destroy');
        Route::post('/{id}/reload', [TenantController::class, 'reload'])->name('admin.tenants.reload');
    });

    // Git & Logout
    Route::post('/git/pull', [DashboardController::class, 'gitPull'])->name('admin.git.pull');
    Route::post('/logout',   [DashboardController::class, 'logout'])->name('admin.logout');
});
