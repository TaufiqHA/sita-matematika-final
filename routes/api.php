<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\v1\PengajuanJudulController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::prefix('v1')->group(function () {
        // Rute untuk Mahasiswa & DPA (Filter list data ditangani di controller)
        Route::get('/pengajuan-judul', [PengajuanJudulController::class, 'index']);
        Route::get('/pengajuan-judul/{id}', [PengajuanJudulController::class, 'show']);

        // Khusus Mahasiswa
        Route::post('/pengajuan-judul', [PengajuanJudulController::class, 'store'])
            ->middleware('role:Mahasiswa');

        // Khusus DPA
        Route::patch('/pengajuan-judul/{id}/review', [PengajuanJudulController::class, 'review'])
            ->middleware('role:DPA');
    });
});
