<?php

use Illuminate\Http\Request;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\LeaseExpenseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

    // Auth
    Route::post('/register', [AuthController::class, 'register']);

    // Users
    Route::get('users', [UserController::class, 'index']);
    Route::post('users/{id}/roles', [UserController::class, 'assignRoles']);

    /*
    |--------------------------------------------------------------------------
    | Building Routes
    |--------------------------------------------------------------------------
    */
  Route::middleware('auth:sanctum')->prefix('buildings')->group(function () {
    Route::get('/', [BuildingController::class, 'index']);
    Route::post('/', [BuildingController::class, 'store']);
    Route::get('/{id}', [BuildingController::class, 'show']);
    Route::put('/{id}', [BuildingController::class, 'update']);
    Route::delete('/{id}', [BuildingController::class, 'destroy']);
});

    /*
    |--------------------------------------------------------------------------
    | Lease Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('leases')->group(function () {
        Route::get('/', [LeaseController::class, 'index']);
        Route::post('/', [LeaseController::class, 'store']);
        Route::get('/{id}', [LeaseController::class, 'show']);
        Route::put('/{id}', [LeaseController::class, 'update']);
        Route::delete('/{id}', [LeaseController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | LeaseExpense Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('expenses')->group(function () {
        Route::get('/', [LeaseExpenseController::class, 'index']);
        Route::post('/', [LeaseExpenseController::class, 'store']);
        Route::get('/{id}', [LeaseExpenseController::class, 'show']);
        Route::put('/{id}', [LeaseExpenseController::class, 'update']);
        Route::delete('/{id}', [LeaseExpenseController::class, 'destroy']);
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Modules, Permissions, Roles)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {

    // Modules
    Route::get('modules', [ModuleController::class, 'index']);
    Route::post('modules', [ModuleController::class, 'store']);
    Route::put('modules/{id}', [ModuleController::class, 'update']);
    Route::delete('modules/{id}', [ModuleController::class, 'destroy']);

    // Permissions
    Route::get('permissions', [PermissionController::class, 'index']);
    Route::post('permissions', [PermissionController::class, 'store']);
    Route::put('permissions/{id}', [PermissionController::class, 'update']);
    Route::delete('permissions/{id}', [PermissionController::class, 'destroy']);

    // Roles
    Route::get('roles', [RoleController::class, 'index']);
    Route::post('roles', [RoleController::class, 'store']);
    Route::put('roles/{id}', [RoleController::class, 'update']);
    Route::delete('roles/{id}', [RoleController::class, 'destroy']);

    // Users (assign roles, list users)
    Route::get('users', [UserController::class, 'index']);
    Route::post('users/{id}/roles', [UserController::class, 'assignRoles']);
});

Route::middleware('auth:sanctum')->prefix('workflow')->group(function () {
    Route::get('/pending', [WorkflowController::class, 'pending']);   // list
    Route::get('/{id}', [WorkflowController::class, 'show']);         // single page
    Route::post('/{id}/approve', [WorkflowController::class, 'approve']);
    Route::post('/{id}/reject',  [WorkflowController::class, 'reject']);
});


Route::middleware('auth:sanctum')->get(
  '/dashboard/stats',
  [DashboardController::class, 'stats']
);