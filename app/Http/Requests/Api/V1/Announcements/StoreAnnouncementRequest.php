<?php

namespace App\Http\Requests\Api\V1\Announcements;

use App\Http\Requests\Api\V1\Announcements\BaseAnnouncementRequest;
use Illuminate\Foundation\Http\FormRequest;

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
            "data.attributes.title" => ["required","string","max:255"],
            "data.attributes.description" => ["required","string","max:2500"],
        ];
    }
}
