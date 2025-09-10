<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AllotmentController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorySpendingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CalculatorController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\SavingsGatewayController;
use App\Http\Controllers\SavingsAdminController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\StockController;

Route::get('/stock/latest', [StockController::class, 'getLatestStockData']);

Route::middleware('auth:sanctum')->get('/export-pdf', [PdfController::class, 'export']);



Route::middleware('auth:sanctum')->get('/mesave', function (Request $request) {
    // Return only the fields you need on the frontend
    return response()->json($request->user()->only([
        'id','name','email','role','job','salary','budget','expenditure','profile_pending_approval'
    ]));
});


Route::get('/banks', [BankController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Allotment routes
    Route::post('/allotments', [AllotmentController::class, 'store']);
    Route::get('/allotments', [AllotmentController::class, 'index']);

    // Goals routes
    Route::get('/goals', [GoalController::class, 'index']);
    Route::post('/goals', [GoalController::class, 'store']);
    Route::patch('/goals/{id}/status', [GoalController::class, 'updateStatus']);

    // Category spending routes
    Route::get('/category-spending', [CategorySpendingController::class, 'index']);

    Route::post('/calculate', [CalculatorController::class, 'calculate']);       // user calc
    Route::post('/convert', [CalculatorController::class, 'convert']);           // user convert
    
    // Admin routes (require role=admin)
    $adminRoutes = function() {
        Route::get('/admin/users/pending', [AdminController::class, 'listPendingUsers']);
        Route::post('/admin/users/approve/{id}', [AdminController::class, 'approveUserProfile']);
        Route::post('/admin/users/reject/{id}', [AdminController::class, 'rejectUserProfile']);
    
        // Admin savings flow
        Route::get('/admin/savings', [SavingsAdminController::class, 'index']);
        Route::post('/admin/savings/{id}/approve', [SavingsAdminController::class, 'approve']);
        Route::post('/admin/savings/{id}/reject', [SavingsAdminController::class, 'reject']);

        // list all rates
        Route::get('/admin/exchange-rates', [ExchangeRateController::class, 'index']);
        // create/update a rate
        Route::post('/admin/exchange-rates', [ExchangeRateController::class, 'upsert']);
        };

    Route::group(['middleware' => function ($request, $next) {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        return $next($request);
    }], $adminRoutes);

    // User routes
    Route::get('/me', [UserController::class, 'showProfile']);
    Route::post('/profile/update', [UserController::class, 'updateProfile']);
    Route::post('/user/savings/submit', [UserController::class, 'submitSavingsRequest']);
    // New route for updating budget
    Route::post('/update-budget', [UserController::class, 'updateBudget']);

    
    // User savings flow
    Route::get('/savings/applications', [SavingsGatewayController::class, 'index']);
    Route::post('/savings/applications', [SavingsGatewayController::class, 'store']);
    
    
});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);