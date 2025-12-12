<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingDetailsController;
use App\Http\Controllers\LeaseDetailsController;   



Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    // Building CRUD
    Route::get('/buildings', [BuildingDetailsController::class, 'index']);
    Route::post('/buildings', [BuildingDetailsController::class, 'store']);
    Route::get('/buildings/{id}', [BuildingDetailsController::class, 'show']);
    Route::put('/buildings/{id}', [BuildingDetailsController::class, 'update']);
    Route::delete('/buildings/{id}', [BuildingDetailsController::class, 'destroy']);

    // Lease CRUD
    Route::get('/leases', [LeaseDetailsController::class, 'index']);
    Route::post('/leases', [LeaseDetailsController::class, 'store']);
    Route::get('/leases/{id}', [LeaseDetailsController::class, 'show']);
    Route::put('/leases/{id}', [LeaseDetailsController::class, 'update']);
    Route::delete('/leases/{id}', [LeaseDetailsController::class, 'destroy']);
});