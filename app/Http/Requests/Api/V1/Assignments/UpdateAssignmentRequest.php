<?php

namespace App\Http\Requests\Api\V1\Assignments;

use App\Http\Requests\Api\V1\Assignments\BaseAssignmentRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class UpdateAssignmentRequest extends BaseAssignmentRequest
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
        $assignment = $this->route('assignment');

        return [
            "data.attributes.subject" => ["sometimes","string","max:255"],
            "data.attributes.content" => ["sometimes","string","max:2500"],
            "data.attributes.dueDate" => [
                "sometimes",
                "date",
                $assignment ? Rule::date()->afterOrEqual($assignment->dueDate) : null,
            ],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'data.attributes.subject' => [
                'type' => "string",
                'description' => 'The subject of the assignment',
                'required' => false, 
                'example' => 'Assignment-3',
            ],
            'data.attributes.content' => [
                'type' => "string",
                'description' => 'The content of the assignment',
                'required' => false, 
                'example' => 'Implement Logistic Regrission',
            ],
            'data.attributes.dueDate' => [
                'type' => "date",
                'description' => 'The due date of the assignment',
                'required' => false, 
                'example' => '2026-05-20',
            ],
        ];
    }
}
