<?php

/**
 * ==========================================================================
 * web.php
 * Deskripsi: Pusat pengaturan rute (routing) aplikasi Brandes.
 *            Dikelola menggunakan middleware role untuk keamanan akses.
 * ==========================================================================
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Shared\HistoryController;
use App\Http\Controllers\Shared\NotifikasiController;
use App\Http\Controllers\Admin\LokasiController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\User\UserController as UserUserController;
use App\Http\Controllers\Api\GpsController;
use App\Http\Controllers\Api\IotRegistrationController;

/*
|--------------------------------------------------------------------------
| 1. AUTHENTICATION ROUTES (Public)
|--------------------------------------------------------------------------
| Mengelola akses masuk, keluar, dan landing page utama.
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| 2. ADMIN ROUTES (Role: Admin)
|--------------------------------------------------------------------------
| Fitur khusus administrator: Manajemen user, admin, dan lokasi brankas.
*/

Route::middleware(['role.admin'])->group(function () {
    
    // --- Dashboard Admin ---
    Route::get('/dashboard/admin', [AdminDashboardController::class, 'index'])->name('dashboard.admin');

    // --- Manajemen Admin ---
    Route::get('/admin/list', [AdminController::class, 'index'])->name('admin.list');
    Route::post('/admin', [AdminController::class, 'store'])->name('admin.store');
    Route::put('/admin/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/admin/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
    Route::patch('/admin/{id}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admin.toggleStatus');

    // --- Manajemen User ---
    Route::get('/user/list', [AdminUserController::class, 'index'])->name('user.list');
    Route::post('/user', [AdminUserController::class, 'store'])->name('user.store');
    Route::put('/user/{id}', [AdminUserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [AdminUserController::class, 'destroy'])->name('user.destroy');
    Route::patch('/user/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('user.toggleStatus');

    // --- Lokasi Brankas ---
    Route::get('/lokasi-brankas', [LokasiController::class, 'index'])->name('lokasi.brankas');

});


/*
|--------------------------------------------------------------------------
| 3. USER ROUTES (Role: User)
|--------------------------------------------------------------------------
| Fitur untuk pengguna biasa: Dashboard pribadi dan manajemen kredensial.
*/

Route::middleware(['role.user'])->group(function () {
    
    // --- Dashboard User ---
    Route::get('/dashboard/user', [UserDashboardController::class, 'index'])->name('dashboard.user');

    // --- Profile & Kredensial ---
    Route::get('/profile/kredensial', [UserUserController::class, 'kredensial'])->name('user.kredensial');

});


/*
|--------------------------------------------------------------------------
| 4. SHARED ROUTES (Authenticated Only)
|--------------------------------------------------------------------------
| Fitur yang dapat diakses oleh Admin maupun User yang sudah login.
*/

Route::middleware(['auth.brandes'])->group(function () {

    Route::get('/history-akses', [HistoryController::class, 'index'])->name('history.akses');
    Route::get('/notifikasi-keamanan', [NotifikasiController::class, 'index'])->name('notifikasi.keamanan');
    Route::get('/notifikasi/download', [NotifikasiController::class, 'download'])->name('notifikasi.download');

});


/*
|--------------------------------------------------------------------------
| 5. API ROUTES (IoT & Mobile Integration)
|--------------------------------------------------------------------------
| Integrasi perangkat ESP32 dan polling data dari sisi frontend.
*/

Route::prefix('api')->group(function () {

    // --- GPS & Real-time Tracking ---
    Route::post('/gps', [GpsController::class, 'store'])->name('api.gps.store');
    Route::get('/gps/{kode_brankas}', [GpsController::class, 'latest'])->name('api.gps.latest');
    Route::get('/brankas/status-realtime', [GpsController::class, 'statusRealtime'])->name('api.brankas.status');

    // --- IoT Device Registration ---
    Route::post('/iot/pendaftaran', [IotRegistrationController::class, 'store'])->name('api.iot.store');
    Route::get('/iot/latest-registration', [IotRegistrationController::class, 'latest'])->name('api.iot.latest');

});