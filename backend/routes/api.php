<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AllotmentController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorySpendingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    // Allotment routes
    Route::post('/allotments', [AllotmentController::class, 'store']);
    Route::get('/allotments', [AllotmentController::class, 'index']);
    // Uncomment the following line if needed
    // Route::post('/allotments/refresh-status', [AllotmentController::class, 'refreshStatus']);

    // Goals routes
    Route::get('/goals', [GoalController::class, 'index']);
    Route::post('/goals', [GoalController::class, 'store']);
    Route::patch('/goals/{id}/status', [GoalController::class, 'updateStatus']);

    // Category spending routes
    Route::get('/category-spending', [CategorySpendingController::class, 'index']);

    // Admin routes
    Route::get('/admin/users/pending', [AdminController::class, 'listPendingUsers']);
    Route::post('/admin/users/approve/{id}', [AdminController::class, 'approveUserProfile']);
    Route::post('/admin/currency/update', [AdminController::class, 'updateCurrencyRate']);
    Route::get('/admin/savings/pending', [AdminController::class, 'listSavingsRequests']);
    Route::post('/admin/savings/approve/{id}', [AdminController::class, 'approveSavingsRequest']);

    // User routes
    Route::get('/me', [UserController::class, 'showProfile']);
    Route::post('/profile/update', [UserController::class, 'updateProfile']);
    Route::post('/user/savings/submit', [UserController::class, 'submitSavingsRequest']);

});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);