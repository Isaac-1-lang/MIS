<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ActivityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // Dashboard Statistics
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/course-stats', [DashboardController::class, 'getCourseStats']);
    
    // Search
    Route::get('/search', [SearchController::class, 'search']);
    
    // Activities
    Route::get('/activities', [ActivityController::class, 'index']);
});
