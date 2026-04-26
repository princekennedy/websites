<?php

use App\Http\Controllers\Api\AppDataController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MeController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::get('/app/config', [AppDataController::class, 'config']);
Route::get('/app/bootstrap', [AppDataController::class, 'bootstrap']);
Route::get('/menus/main', [AppDataController::class, 'mainMenu']);
Route::get('/contents', [AppDataController::class, 'contents']);
Route::get('/contents/{slug}', [AppDataController::class, 'showContent']);
Route::get('/categories', [AppDataController::class, 'categories']);
Route::get('/notifications', [AppDataController::class, 'notifications']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/me', [MeController::class, 'show']);
    Route::get('/me/permissions', [MeController::class, 'permissions']);
    Route::get('/me/bootstrap', [AppDataController::class, 'bootstrap']);
});