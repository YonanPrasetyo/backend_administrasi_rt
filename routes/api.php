<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RumahController;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\DashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('test', function() {
    return response()->json(['message' => 'POST works']);
});

Route::get('dashboard', [DashboardController::class, 'index']);

Route::get('rumah', [RumahController::class, 'index']);
Route::get('rumah/{id}', [RumahController::class, 'show']);
Route::post('rumah', [RumahController::class, 'store']);
Route::put('rumah/{id}', [RumahController::class, 'update']);
Route::delete('rumah/{id}', [RumahController::class, 'destroy']);
Route::post('rumah/{id}/tambah_penghuni', [RumahController::class, 'add_penghuni']);
Route::post('rumah/{id}/hapus_penghuni', [RumahController::class, 'remove_penghuni']);

Route::get('penghuni', [PenghuniController::class, 'index']);
Route::get('penghuni/{id}', [PenghuniController::class, 'show']);
Route::post('penghuni', [PenghuniController::class, 'store']);
Route::match(['put', 'post'], 'penghuni/{id}', [PenghuniController::class, 'update']);
Route::delete('penghuni/{id}', [PenghuniController::class, 'destroy']);

Route::get('pembayaran', [PembayaranController::class, 'index']);
Route::post('pembayaran', [PembayaranController::class, 'pembayaran']);
Route::delete('pembayaran/{id}', [PembayaranController::class, 'destroy']);

Route::get('pengeluaran', [PengeluaranController::class, 'index']);
Route::get('pengeluaran/{id}', [PengeluaranController::class, 'show']);
Route::post('pengeluaran', [PengeluaranController::class, 'store']);
Route::put('pengeluaran/{id}', [PengeluaranController::class, 'update']);
Route::delete('pengeluaran/{id}', [PengeluaranController::class, 'destroy']);
