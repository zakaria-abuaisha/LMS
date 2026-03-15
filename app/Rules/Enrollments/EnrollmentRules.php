<?php

namespace App\Rules\Enrollments;

use App\Models\Course;
use App\Models\Enrollment;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth as Auth;

class EnrollmentRules implements ValidationRule, DataAwareRule
{

    protected array $data = [];

    public function setData(array $data):static {
        $this->data = $data ;
        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $courseCode = data_get($this->data, "data.attributes.courseCode");
        $userId = Auth::user()->id;
        $course = Course::select("id", "instructor_id")->where("course_code", $courseCode)->first();
        $enrollment = Enrollment::select("id")->where("student_id", $userId)->where("course_id", $course->id)->first();
    
        // Check if the applyer is not the course creator.
        if ($userId === $course->instructor_id) {
            $fail("The Instructor can not be a student!");
        }
        // Check if the user has alread been enrolled
        else if ($enrollment) {
            $fail("You're Already Enrolled!");
        }
    }
}
