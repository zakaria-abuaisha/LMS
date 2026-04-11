<?php

namespace App\Http\Requests\Api\V1\Assignments;

use App\Http\Requests\Api\V1\Assignments\BaseAssignmentRequest;
use Illuminate\Validation\Rule;

class StoreAssignmentRequest extends BaseAssignmentRequest
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
            "data.attributes.subject" => ["required","string","max:255"],
            "data.attributes.content" => ["required","string","max:2500"],
            "data.attributes.dueDate" => ["required","date", Rule::date()->afterOrEqual(today()->addDays(1))],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'data.attributes.subject' => [
                'type' => "string",
                'description' => 'The subject of the assignment',
                'required' => true, 
                'example' => 'Assignment-3',
            ],
            'data.attributes.content' => [
                'type' => "string",
                'description' => 'The content of the assignment',
                'required' => true, 
                'example' => 'Implement Logistic Regrission',
            ],
            'data.attributes.dueDate' => [
                'type' => "date",
                'description' => 'The due date of the assignment',
                'required' => true, 
                'example' => '2026-05-20',
            ],
        ];
    }
}
