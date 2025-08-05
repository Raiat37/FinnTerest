<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AllotmentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/allotments', [AllotmentController::class, 'store']);
Route::post('/allotments/refresh-status', [AllotmentController::class, 'refreshStatus']);

Route::get('/allotments', [AllotmentController::class, 'index']);