<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::get('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

    // News Data
    Route::get('sources', [\App\Http\Controllers\Api\SourceController::class, 'index']);
    Route::get('categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::get('authors', [\App\Http\Controllers\Api\AuthorController::class, 'index']);
    Route::get('news', [\App\Http\Controllers\Api\NewsController::class, 'index']);
});
