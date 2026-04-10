<?php

namespace App\Http\Requests\Api\V1\Courses;

use App\Rules\Courses\SumsToHundred;
use Illuminate\Validation\Rule;

/**
 * @bodyParam data.attributes.courseName string required The course name. Example: Machine Learning(ML).
 * @bodyParam data.attributes.description string required The course description. Example: Teach the machines.
 * @bodyParam data.attributes.startAt string required The course starting date. Example: 2026-4-5
 * @bodyParam data.attributes.endAt string required The course ending date. Example: 2026-7-30
 * @bodyParam data.attributes.assignmentPercent integer required The percentage of the assignments in the course. Example: 30
 * @bodyParam data.attributes.quizPercent integer required The percentage of the quizzes in the course. Example: 10
 * @bodyParam data.attributes.midPercent integer required The percentage of the mid exam in the course. Example: 20
 * @bodyParam data.attributes.finalPercent integer required The percentage of the final exam in the course. Example: 40
 */
class StoreCourseRequest extends BaseCourseRequest
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
            'data.attributes.courseName' => ["required", "string", "max:255"],
            'data.attributes.description' => ["sometimes", "string", "max:2000"],
            'data.attributes.startAt' => ["required", "date", Rule::date()->afterOrEqual(today()->addDays(1))],
            'data.attributes.endAt' => ["required", "date", "after:data.attributes.startAt"],
            'data.attributes.assignmentPercent' => ["required", "numeric", "between:0,100"],
            'data.attributes.quizPercent' => ["required", "numeric", "between:0,100"],
            'data.attributes.midPercent' => ["required", "numeric", "between:0,100"],
            'data.attributes.finalPercent' => ["required", "numeric", "between:0,100", new SumsToHundred],
        ];
    }
}
