<?php

namespace App\Http\Requests\Api\V1\GradeSubmissions;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeSubmissionRequest extends FormRequest
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
            "data" => ["required", "array"],
            "data.attributes" => ["required", "array"],
            "data.attributes.grade" => ["required","numeric","between:0,100"],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'data.attributes.grade' => [
                'type' => "numeric",
                'description' => 'The grade of a submission, between 0 and 100.',
                'required' => true, 
                'example' => 76,
            ]
        ];
    }
}
