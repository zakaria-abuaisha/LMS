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
            "data.attributes.grade" => ["sometimes", "between:0,100"] 
        ];
    }
}
