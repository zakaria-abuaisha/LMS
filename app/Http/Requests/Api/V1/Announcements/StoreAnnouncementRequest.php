<?php

namespace App\Http\Requests\Api\V1\Announcements;

use App\Http\Requests\Api\V1\Announcements\BaseAnnouncementRequest;

class StoreAnnouncementRequest extends BaseAnnouncementRequest
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
            "data.attributes.title" => ["required","string","max:255"],
            "data.attributes.description" => ["required","string","max:2500"],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'data.attributes.title' => [
                'type' => "string",
                'description' => 'The title of the announcement',
                'required' => true, 
                'example' => 'Adjustments on assignment 3',
            ],
            'data.attributes.description' => [
                'type' => "string",
                'description' => 'The description of the announcement!',
                'required' => true, 
                'example' => 'dear students, There are some changes on the assignment number 3, please check them out!',
            ],
        ];
    }
}
