<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

/* ═══════════════════════════════════════
   AUTH ROUTES (publik)
═══════════════════════════════════════ */

// '/' redirect ke '/login'
Route::get('/', function () {
    return redirect()->route('login');
});

// Form login — GET tampilkan, POST proses
// Keduanya pakai URL yang sama '/login' agar form action="{{ route('login') }}" langsung POST ke sini
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/* ═══════════════════════════════════════
   ADMIN ROUTES
═══════════════════════════════════════ */
Route::middleware(['auth.brandes:admin'])->group(function () {

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
   USER ROUTES
═══════════════════════════════════════ */
Route::middleware(['auth.brandes:user'])->group(function () {

    Route::get('/dashboard/user', [DashboardController::class, 'user'])
         ->name('dashboard.user');

});

/* ═══════════════════════════════════════
   SHARED ROUTES (admin + user)
═══════════════════════════════════════ */
Route::middleware(['auth.brandes'])->group(function () {

    Route::get('/history-akses',       [HistoryController::class, 'index'])->name('history.akses');
    Route::get('/notifikasi-keamanan', [NotifikasiController::class, 'index'])->name('notifikasi.keamanan');
    Route::get('/lokasi-brankas',      [LokasiController::class, 'index'])->name('lokasi.brankas');

});