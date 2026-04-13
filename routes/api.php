<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\v1\NotificationController;
use App\Http\Controllers\Api\v1\PendaftaranSeminarController;
use App\Http\Controllers\Api\v1\PengajuanJudulController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::prefix('v1')->group(function () {
        // ... (existing pengajuan-judul routes)
        Route::get('/pengajuan-judul', [PengajuanJudulController::class, 'index']);
        Route::get('/pengajuan-judul/{id}', [PengajuanJudulController::class, 'show']);
        Route::post('/pengajuan-judul', [PengajuanJudulController::class, 'store'])
            ->middleware('role:Mahasiswa');
        Route::patch('/pengajuan-judul/{id}/review', [PengajuanJudulController::class, 'review'])
            ->middleware('role:DPA');

        // Pendaftaran Seminar
        Route::prefix('seminar')->group(function () {
            // List pendaftaran seminar (di-filter role pada controller)
            Route::get('/pendaftaran', [PendaftaranSeminarController::class, 'index']);
            Route::get('/pendaftaran/{id}', [PendaftaranSeminarController::class, 'show']);

            // Khusus Mahasiswa
            Route::post('/pendaftaran', [PendaftaranSeminarController::class, 'store'])
                ->middleware('role:Mahasiswa');
            Route::post('/pendaftaran/{id}/berkas', [PendaftaranSeminarController::class, 'uploadBerkasTambahan'])
                ->middleware('role:Mahasiswa');

            // Khusus TU
            Route::patch('/pendaftaran/{id}/verifikasi', [PendaftaranSeminarController::class, 'verifikasi'])
                ->middleware('role:TU');
        });

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    });
});
