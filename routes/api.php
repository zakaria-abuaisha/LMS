<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\AnnouncementsController;
use App\Http\Controllers\Api\V1\AssignmentController;
use App\Http\Controllers\Api\V1\AssignmentFileController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\CoursesController;
use App\Http\Controllers\Api\V1\DiscussionsController;
use App\Http\Controllers\Api\V1\EnrollmentsController;
use App\Http\Controllers\Api\V1\LecturesController;
use App\Http\Controllers\Api\V1\StudentSubmissionController;
use App\Http\Controllers\Api\V1\SubmissionFilesController;
use App\Http\Controllers\Api\V1\SubmissionsController;
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

            // Assignemtns 
            Route::get("/{course}/assignments", [AssignmentController::class,"index"]);
            Route::post("/{course}/assignments/register", [AssignmentController::class,"store"]);
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

        // Course Assignments
        Route::prefix("assignments")->group(function () {
            Route::get("/{assignment}", [AssignmentController::class,"show"]);
            Route::patch("/{assignment}", [AssignmentController::class,"update"]);
            Route::delete("/{assignment}", [AssignmentController::class,"destroy"]);

            // Files
            Route::get("/{assignment}/assignmentFiles", [AssignmentFileController::class, "index"]);
            Route::post("/{assignment}/assignmentFiles/register", [AssignmentFileController::class, "store"]);

            // Submissions
            Route::get("/{assignment}/submissions", [SubmissionsController::class, "index"]);
            Route::post("/{assignment}/studentSubmission/register", [StudentSubmissionController::class, "store"]);

        });

        // Assignment Submission
        Route::prefix("submissions")->group(function () {
            Route::get("/{submission}", [SubmissionsController::class, "show"]);
            Route::patch("/grade/{submission}", [SubmissionsController::class, "grade"]);

            // Assignment Submissions For Student
            Route::get("/{submission}/studentSubmission", [StudentSubmissionController::class, "show"]);
            Route::delete("/{submission}/studentSubmission", [StudentSubmissionController::class, "destroy"]);

            // Submission Files 
            Route::get("/{submission}/submissionFile", [SubmissionFilesController::class, "index"]);
            Route::post("/{submission}/submissionFile/register", [SubmissionFilesController::class, "store"]);
        });

        // Submission Files
        Route::prefix("submissionFile")->group(function () {
            Route::get("/{submissionFile}", [SubmissionFilesController::class, "show"]);
            Route::get("/download/{submissionFile}", [SubmissionFilesController::class,"downloadSubmissionFile"]);
            Route::delete("/{submissionFile}", [SubmissionFilesController::class, "destroy"]);

        });

        // Assignment Files
        Route::prefix("assignmentFiles")->group(function () {
            Route::get("/{assignmentFile}", [AssignmentFileController::class, "show"]);
            Route::get("/download/{assignmentFile}", [AssignmentFileController::class, "downloadAssignmentFile"]);
            Route::delete("/{assignmentFile}", [AssignmentFileController::class,"destroy"]);
        });

        // Discussion Comments
        Route::prefix("comments")->group(function () {
            Route::get("/{comment}", [CommentController::class, "show"]);
            Route::patch("/{comment}", [CommentController::class, "update"]);
            Route::delete("/{comment}", [CommentController::class,"destroy"]);
        });
    });
});