<?php

namespace App\Http\Requests;

use App\Http\Requests\Api\V1\Courses\BaseEnrollmentRequest;
use App\Rules\Enrollments\CheckIfInstructor;

class StoreEnrollmentRequest extends BaseEnrollmentRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "data.attributes.courseCode" => ["required", "exists:courses,course_code", new CheckIfInstructor] 
        ];
    }
}
