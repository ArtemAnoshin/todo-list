<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('login', [AuthController::class, 'login']);

    // Маршруты для задач
    Route::apiResource('tasks', TaskController::class);
});

Route::group([
    'middleware' => ['auth:api'],
    'prefix' => 'auth'
], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::group([
    'middleware' => ['auth:api'],
], function () {
    Route::get('user', [AuthController::class, 'me']);
});
