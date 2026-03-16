<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\AnnouncementsController;
use App\Http\Controllers\Api\V1\CoursesController;
use App\Http\Controllers\Api\V1\EnrollmentsController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware("auth:sanctum")->group(function () {
    // Logout
    Route::post("/logout", [AuthController::class,"logout"]);

    // Version 1
    Route::prefix("V1")->group(function () {

        // Courses
        Route::prefix("courses")->group(function () {
            Route::get("", [CoursesController::class,"index"]);
            Route::post("/register", [CoursesController::class, "store"]);
            Route::get("/{course}", [CoursesController::class, "show"]);
            Route::patch("/{course}", [CoursesController::class,"update"]);
            Route::delete("/{course}", [CoursesController::class,"destroy"]);

            // Announcements
            Route::get("/{course}/announcements", [AnnouncementsController::class,"index"]);
            Route::post("/{course}/announcements/register", [AnnouncementsController::class, "store"]);
        });

        // Enrollments
        Route::prefix("enrollments")->group(function () {
            Route::post("/register", [EnrollmentsController::class, "store"]);
            Route::get("/{enrollment}", [EnrollmentsController::class,"show"]);
            Route::delete("/{enrollment}", [EnrollmentsController::class,"destroy"]);
        });

        // Course Announcements
        Route::prefix("announcements")->group(function () {
            Route::get("/{announcement}", [AnnouncementsController::class, "show"]);
            Route::patch("/{announcement}", [AnnouncementsController::class,"update"]);
            Route::delete("/{announcement}", [AnnouncementsController::class,"destroy"]);
        });

    });
});