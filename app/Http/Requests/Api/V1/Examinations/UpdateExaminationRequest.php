<?php

namespace App\Http\Requests\Api\V1\Examinations;

use App\Enums\ExaminationType;
use App\Http\Requests\Api\V1\Examinations\BaseExaminationRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateExaminationRequest extends BaseExaminationRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /*
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "data.attributes.type" => ["sometimes", new Enum(ExaminationType::class)],
            "data.attributes.grade" => ["sometimes", "numeric", "between:0,100"] 
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'data.attributes.type' => [
                'type' => "string",
                'description' => 'The type of the examination (quiz, mid, final).',
                'required' => false, 
                'example' => 'quiz',
            ],
            'data.attributes.grade' => [
                'type' => 'numeric',
                'description' => 'The grade of the examination, between 0 and 100.',
                'required' => false, 
                'example' => 65,
            ],
        ];
    }
}
