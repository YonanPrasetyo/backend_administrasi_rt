<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RumahController;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengeluaranController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('test', function() {
    return response()->json(['message' => 'POST works']);
});

Route::get('rumah', [RumahController::class, 'index']);
Route::get('rumah/{id}', [RumahController::class, 'show']);
Route::post('rumah', [RumahController::class, 'store']);
Route::put('rumah/{id}', [RumahController::class, 'update']);
Route::delete('rumah/{id}', [RumahController::class, 'destroy']);

