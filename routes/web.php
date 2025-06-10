<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// --- Controller Autentikasi Kustom ---
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;

// --- Controller Aplikasi Lainnya ---
use App\Http\Controllers\DashboardController;

// --- Admin Controllers ---
use App\Http\Controllers\Admin\ProdiController;
use App\Http\Controllers\Admin\JenisDokumenController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DosenController as AdminDosenController;
use App\Http\Controllers\Admin\MahasiswaController as AdminMahasiswaController;

// --- Mahasiswa Controllers ---
use App\Http\Controllers\Mahasiswa\RequestJudulController as MahasiswaRequestJudulController;
use App\Http\Controllers\Mahasiswa\RequestBimbinganController as MahasiswaRequestBimbinganController;
use App\Http\Controllers\Mahasiswa\DokumenController as MahasiswaDokumenController;
use App\Http\Controllers\Mahasiswa\HistoryBimbinganController as MahasiswaHistoryBimbinganController;

// --- Dosen Controllers ---
use App\Http\Controllers\Dosen\RequestJudulController as DosenRequestJudulController;
use App\Http\Controllers\Dosen\RequestBimbinganController as DosenRequestBimbinganController;
use App\Http\Controllers\Dosen\HistoryBimbinganController as DosenHistoryBimbinganController;
use App\Http\Controllers\Dosen\ReviewDokumenController as DosenReviewDokumenController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Rute Autentikasi Kustom ---
Route::middleware('guest')->group(function () {
    Route::get('login', [CustomLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [CustomLoginController::class, 'login']);
});
Route::post('logout', [CustomLoginController::class, 'logout'])->middleware('auth')->name('logout');

// Rute untuk menampilkan form permintaan reset password
Route::get('lupa-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');

// Rute untuk memproses pengiriman email reset password
Route::post('lupa-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Rute untuk menampilkan form reset password (yang ada tokennya)
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');

// Rute untuk memproses penyimpanan password baru
Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

// --- Halaman Awal ---
Route::get('/', function () {
    if (Auth::check()) { return redirect()->route('dashboard'); }
    return redirect()->route('login');
});

// --- Rute yang Memerlukan Autentikasi ---
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Rute Spesifik Admin ---
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('prodi', ProdiController::class);
        Route::resource('jenis-dokumen', JenisDokumenController::class)->except(['show']);
        Route::resource('users', AdminUserController::class);
        Route::resource('dosen', AdminDosenController::class);
        Route::resource('mahasiswa', AdminMahasiswaController::class);
        // Menghapus duplikasi route dokumen yang ada di dalam blok admin
    });

    // --- Rute Spesifik Dosen ---
    Route::middleware(['dosen'])->prefix('dosen')->name('dosen.')->group(function () {
        Route::resource('request-judul', DosenRequestJudulController::class)->only(['index', 'show', 'edit', 'update']);
        Route::resource('request-bimbingan', DosenRequestBimbinganController::class)->only(['index', 'show', 'edit', 'update']);
        // Menghapus duplikasi resource history-bimbingan
        Route::resource('history-bimbingan', DosenHistoryBimbinganController::class)->except(['create', 'store', 'destroy']);
        Route::get('review-dokumen', [DosenReviewDokumenController::class, 'index'])->name('review-dokumen.index');
        Route::get('review-dokumen/{dokumenProyekAkhir}/proses', [DosenReviewDokumenController::class, 'prosesReview'])->name('review-dokumen.proses');
        Route::put('review-dokumen/{dokumenProyekAkhir}', [DosenReviewDokumenController::class, 'updateReview'])->name('review-dokumen.update');
    });

    // --- Rute Spesifik Mahasiswa ---
    Route::middleware(['mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {

        // --- PENYESUAIAN UTAMA ADA DI SINI ---
        // Mendefinisikan nama parameter secara eksplisit untuk konsistensi
        Route::resource('request-judul', MahasiswaRequestJudulController::class)
            ->parameters(['request-judul' => 'requestJudul']);

        Route::resource('request-bimbingan', MahasiswaRequestBimbinganController::class)
            ->parameters(['request-bimbingan' => 'requestBimbingan']);

        Route::resource('dokumen', MahasiswaDokumenController::class)->parameters([
            'dokumen' => 'dokumenProyekAkhir'
        ]);

        Route::resource('history-bimbingan', MahasiswaHistoryBimbinganController::class)
            ->only(['index', 'show'])
            ->parameters(['history-bimbingan' => 'historyBimbingan']); // Konsistensi untuk semua
    });

    Route::post('/notifications/mark-all-read', function(Request $request) {
        if (Auth::check()) { Auth::user()->unreadNotifications->markAsRead(); }
        return back();
    })->name('notifications.markAllRead')->middleware('auth');
});
