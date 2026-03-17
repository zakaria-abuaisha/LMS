<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class CoursePolicy
{
    public function IsForInstructor(User $user, Course $course) 
    {
        return $user->id === $course->instructor_id;
    }

    public function IsStudentEnrolled(User $user, Course $course): bool
    {
        return Enrollment::where("student_id", $user->id)
                ->where("course_id", $course->id)->exists();
    }
}
