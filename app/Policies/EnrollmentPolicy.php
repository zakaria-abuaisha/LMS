<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    public function isBelongsToStudent(User $user, Enrollment $enrollment): bool
    {
        return $user->id === $enrollment->student_id;
    }

    public function isTheInstructor(User $user, Course $course): bool
    {
        return $course->instructor_id === $user->id;
    }
}
