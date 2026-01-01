<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Outlet\PaymentController;
use App\Http\Controllers\Api\V1\Outlet\ReportController;
use App\Http\Controllers\Api\V1\Outlet\SaleController;
use App\Http\Controllers\Api\V1\Outlet\ShiftController;
use App\Http\Controllers\Api\V1\Outlet\StockController;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });

    Route::middleware(['auth:sanctum', 'ensure.outlet'])
        ->prefix('outlets/{outletId}')
        ->group(function () {
            Route::get('/stocks', [StockController::class, 'index']);
            Route::post('/stocks/adjust', [StockController::class, 'adjust']);

            Route::post('/sales', [SaleController::class, 'store']);
            Route::get('/sales/{saleId}', [SaleController::class, 'show']);
            Route::post('/sales/{saleId}/items', [SaleController::class, 'addItem']);

            Route::post('/sales/{saleId}/pay/cash', [PaymentController::class, 'payCash']);
            Route::post('/sales/{saleId}/pay/qris', [PaymentController::class, 'payQris']);

            Route::post('/shifts/open', [ShiftController::class, 'open']);
            Route::post('/shifts/{shiftId}/close', [ShiftController::class, 'close']);

            Route::get('/reports/sales-summary', [ReportController::class, 'salesSummary']);
        });
});
