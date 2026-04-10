<?php

namespace App\Http\Requests\Api\V1\Courses;

use App\Rules\Courses\SumsToHundred;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends BaseCourseRequest
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
            'data.attributes.courseName' => ["sometimes", "string", "max:255"],
            'data.attributes.description' => ["sometimes", "string", "max:2000"],
            'data.attributes.startAt' => ["required_with:data.attributes.endAt", "date", Rule::date()->afterOrEqual(today()->addDays(1))],
            'data.attributes.endAt' => ["required_with:data.attributes.startAt", "date", "after:data.attributes.startAt"],
            'data.attributes.assignmentPercent' => [
                "required_with:data.attributes.quizPercent,data.attributes.midPercent,data.attributes.finalPercent", 
                "numeric", 
                "between:0,100"],
            'data.attributes.quizPercent' => [
                "required_with:data.attributes.assignmentPercent,data.attributes.midPercent,data.attributes.finalPercent", 
                "numeric", 
                "between:0,100"],
            'data.attributes.midPercent' => [
                "required_with:data.attributes.assignmentPercent,data.attributes.quizPercent,data.attributes.finalPercent", 
                "numeric", 
                "between:0,100"],
            'data.attributes.finalPercent' => [
                "required_with:data.attributes.assignmentPercent,data.attributes.quizPercent,data.attributes.midPercent", 
                "numeric", 
                "between:0,100", 
                new SumsToHundred],
        ];
    }
    public function bodyParameters(): array
    {
        return [
            'data.attributes.courseName' => [
                'type' => 'string',
                'description' => 'The course name',
                'required' => false, 
                'example' => 'Machine Learning (ML)',
            ],
            'data.attributes.description' => [
                'type' => 'string',
                'description' => 'The course description',
                'required' => false,
                'example' => 'Learn the foundations of AI.',
            ],
            'data.attributes.startAt' => [
                'type' => 'date',
                'description' => 'Course start date. Must be at least tomorrow.',
                'required' => 'Required if endAt is present.',
                'example' => '2026-05-01',
            ],
            'data.attributes.endAt' => [
                'type' => 'date',
                'description' => 'Course end date. Must be after the start date.',
                'required' => 'Required if startAt is present.',
                'example' => '2026-08-01',
            ],
            'data.attributes.assignmentPercent' => [
                'type' => 'numeric',
                'description' => 'Percentage weight of assignments.',
                'required' => 'Required if any other percentage field is provided.',
                'example' => 30,
            ],
            'data.attributes.quizPercent' => [
                'type' => 'numeric',
                'description' => 'Percentage weight of quizzes.',
                'required' => 'Required if any other percentage field is provided.',
                'example' => 10,
            ],
            'data.attributes.midPercent' => [
                'type' => 'numeric',
                'description' => 'Percentage weight of the midterm.',
                'required' => 'Required if any other percentage field is provided.',
                'example' => 20,
            ],
            'data.attributes.finalPercent' => [
                'type' => 'numeric',
                'description' => 'Percentage weight of the final exam.',
                'required' => 'Required if any other percentage field is provided.',
                'example' => 40,
                'notes' => [
                    'Custom Rule: The sum of all percentage fields must equal exactly 100.'
                ],
            ],
        ];
    }
}
