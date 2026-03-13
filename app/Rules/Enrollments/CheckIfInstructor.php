<?php

namespace App\Rules\Enrollments;

use App\Models\Course;
use Auth;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckIfInstructor implements ValidationRule, DataAwareRule
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
        $userId = Auth::user()->id;
        $courseInstructorId = Course::where("course_code", data_get($this->data, "data.attributes.courseCode"))->instructor_id;

        if ($userId === $courseInstructorId) {
            $fail("The Instructor can not be a student!");
        }
    }
}
