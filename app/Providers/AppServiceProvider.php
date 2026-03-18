<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Course;
use App\Models\Discussion;
use App\Policies\CourseAnnouncementPolicy;
use App\Policies\CourseLecturePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Course::class, UserPolicy::class);
        Gate::policy(Discussion::class, UserPolicy::class);
        Gate::policy(Comment::class, UserPolicy::class);
    }
}
