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
                Rule::date()->afterOrEqual($assignment->dueDate),
            ],
        ];
    }
}
