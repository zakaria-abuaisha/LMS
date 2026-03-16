<?php

namespace App\Providers;

use App\Models\Course;
use App\Policies\CourseAnnouncementPolicy;
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
        // Must Be registed
        Gate::policy(Course::class, CourseAnnouncementPolicy::class);
    }
}
