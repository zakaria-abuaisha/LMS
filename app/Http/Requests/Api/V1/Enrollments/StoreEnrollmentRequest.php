<?php

namespace App\Http\Requests\Api\V1\Enrollments;

use App\Http\Requests\Api\V1\Enrollments\BaseEnrollmentRequest;
use App\Rules\Enrollments\EnrollmentRules;

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
            'data' => ["required", "array"],
            'data.attributes' => ["required", "array"],
            "data.attributes.courseCode" => ["bail", "required", "exists:courses,course_code", new EnrollmentRules] 
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'data.attributes.courseCode' => [
                'type' => 'string',
                'description' => 'The unique code of the course. Must exist in the courses table.',
                'required' => true, 
                'example' => 'cxb8Q4hze',
            ],
        ];
    }
}
