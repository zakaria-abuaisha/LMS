<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function isBelongsToUser(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id;
    }
}
