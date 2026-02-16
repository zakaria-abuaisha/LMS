<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\CoursesController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware("auth:sanctum")->group(function () {
    // Logout
    Route::post("/logout", [AuthController::class,"logout"]);

    // Version 1
    Route::prefix("V1")->group(function () {

        Route::prefix("courses")->group(function () {
            Route::post("/register", [CoursesController::class, "store"]);
            Route::get("/{course}", [CoursesController::class, "show"]);
        });
    });
});