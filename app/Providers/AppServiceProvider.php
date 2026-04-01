<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Discussion;
use App\Policies\EnrollmentPolicy;
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
        Gate::policy(Course::class, EnrollmentPolicy::class);
        Gate::policy(Discussion::class, UserPolicy::class);
        Gate::policy(Comment::class, UserPolicy::class);
        Gate::policy(Assignment::class, UserPolicy::class);
    }
}
