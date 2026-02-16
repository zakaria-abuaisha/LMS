<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewCourse(User $user, Course $course): bool
    {
        return $user->id === $course->instructure_id;
    }
}
