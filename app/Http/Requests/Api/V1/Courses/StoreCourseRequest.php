<?php

namespace App\Http\Requests\Api\V1\Courses;

use App\Rules\SumsToHundred;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
