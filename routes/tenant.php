<?php
declare(strict_types=1);
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('tenant.login');
Route::post('/login', [AuthController::class, 'login'])->name('tenant.login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('tenant.logout');

Route::get('/', function () {
    if (!session('tenant_user')) return redirect()->route('tenant.login');
    return redirect()->route('tenant.dashboard');
});