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
            "data.attributes.subject" => ["required","string","max:255"],
            "data.attributes.content" => ["required","string","max:2500"],
            "data.attributes.dueDate" => ["required","date",Rule::date()->afterOrEqual(today()->addDays(1))],
        ];
    }
}
