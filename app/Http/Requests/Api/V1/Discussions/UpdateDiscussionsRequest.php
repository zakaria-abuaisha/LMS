<?php

namespace App\Http\Requests\Api\V1\Discussions;

class UpdateDiscussionsRequest extends BaseDiscussionsRequest
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
            'data.attributes.content' => ["sometimes", "string", "max:5000"],
        ];
    }
}
