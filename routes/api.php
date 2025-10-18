<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PengajuanSuratController; // Pastikan ini di-import

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

// Middleware 'api' sudah diset secara default untuk semua rute di sini

// Route untuk Midtrans Webhook Notification
// Midtrans SELALU menggunakan method POST
Route::post('/midtrans-callback', [PengajuanSuratController::class, 'midtransCallback']);


// Anda bisa membiarkan route default Laravel di bawah (jika ada)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
