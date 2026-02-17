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
}
