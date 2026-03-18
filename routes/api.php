<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\AnnouncementsController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\CoursesController;
use App\Http\Controllers\Api\V1\DiscussionsController;
use App\Http\Controllers\Api\V1\EnrollmentsController;
use App\Http\Controllers\Api\V1\LecturesController;
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

            // Lectures
            Route::get("/{course}/lectures", [LecturesController::class,"index"]);
            Route::post("/{course}/lectures/register", [LecturesController::class,"store"]);

            // Discussions
            Route::get("/{course}/discussions", [DiscussionsController::class,"index"]);
            Route::post("/{course}/discussions/register", [DiscussionsController::class,"store"]);

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

        // Course Lectures
        Route::prefix("lectures")->group(function () {
            Route::get("/{lecture}", [LecturesController::class, "show"]);
            Route::get("/download/{lecture}", [LecturesController::class, "downloadLectureFile"]);
            Route::delete("/{lecture}", [LecturesController::class,"destroy"]);
        });

        // Course Discussions
        Route::prefix("discussions")->group(function () {
            Route::get("/{discussion}", [DiscussionsController::class, "show"]);
            Route::patch("/{discussion}", [DiscussionsController::class, "update"]);
            Route::delete("/{discussion}", [DiscussionsController::class,"destroy"]);

            // Comments
            Route::get("/{discussion}/comments", [CommentController::class,"index"]);
            Route::post("/{discussion}/comments/register", [CommentController::class,"store"]);
        });

        // Discussion Comments
        Route::prefix("comments")->group(function () {
            Route::get("/{comment}", [CommentController::class, "show"]);
            Route::patch("/{comment}", [CommentController::class, "update"]);
            Route::delete("/{comment}", [CommentController::class,"destroy"]);
        });
    });
});