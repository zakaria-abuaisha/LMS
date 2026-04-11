<?php

namespace App\Http\Requests\Api\V1\Discussions;

class StoreDiscussionsRequest extends BaseDiscussionsRequest
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
            'data.attributes.content' => ["required", "string", "max:5000"],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'data.attributes.content' => [
                'type' => "string",
                'description' => 'The content of the discussion',
                'required' => true, 
                'example' => 'I think I enhanced the convergence of gradient descent',
            ]
        ];
    }
}
