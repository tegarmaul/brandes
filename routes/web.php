<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GpsController;

/* ═══════════════════════════════════════
   AUTH ROUTES (publik)
═══════════════════════════════════════ */

// '/' redirect ke '/login'
Route::get('/', function () {
    return redirect()->route('login');
});

// Form login — GET tampilkan, POST proses
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/* ═══════════════════════════════════════
   ADMIN ROUTES — hanya role admin
═══════════════════════════════════════ */
Route::middleware(['role.admin'])->group(function () {

    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
         ->name('dashboard.admin');

    Route::get('/admin/list',      [AdminController::class, 'index'])->name('admin.list');
    Route::post('/admin',          [AdminController::class, 'store'])->name('admin.store');
    Route::put('/admin/{id}',      [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/admin/{id}',   [AdminController::class, 'destroy'])->name('admin.destroy');

    Route::get('/user/list',       [UserController::class, 'index'])->name('user.list');
    Route::post('/user',           [UserController::class, 'store'])->name('user.store');
    Route::put('/user/{id}',       [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}',    [UserController::class, 'destroy'])->name('user.destroy');

});

/* ═══════════════════════════════════════
   USER ROUTES — hanya role user biasa
═══════════════════════════════════════ */
Route::middleware(['role.user'])->group(function () {

    Route::get('/dashboard/user', [DashboardController::class, 'user'])
         ->name('dashboard.user');

});

/* ═══════════════════════════════════════
   SHARED ROUTES (admin + user, cukup login)
═══════════════════════════════════════ */
Route::middleware(['auth.brandes'])->group(function () {

    Route::get('/history-akses',       [HistoryController::class, 'index'])->name('history.akses');
    Route::get('/notifikasi-keamanan', [NotifikasiController::class, 'index'])->name('notifikasi.keamanan');
    Route::get('/lokasi-brankas',      [LokasiController::class, 'index'])->name('lokasi.brankas');

});

/* ═══════════════════════════════════════
   API ROUTES — untuk perangkat IoT (ESP32)
   Tidak memerlukan session/cookie, menggunakan token header
═══════════════════════════════════════ */
Route::prefix('api')->group(function () {

    // POST /api/gps — ESP32 kirim data GPS Neo-6M
    // Header: X-Device-Token: {GPS_DEVICE_TOKEN dari .env}
    Route::post('/gps', [GpsController::class, 'store'])->name('api.gps.store');

    // GET /api/gps/{kode_brankas} — polling data GPS terbaru dari frontend
    Route::get('/gps/{kode_brankas}', [GpsController::class, 'latest'])->name('api.gps.latest');

});
