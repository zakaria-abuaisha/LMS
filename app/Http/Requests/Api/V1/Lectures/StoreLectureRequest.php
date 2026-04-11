<?php

namespace App\Http\Requests\Api\V1\Lectures;

use App\Http\Requests\Api\V1\Lectures\BaseLectureRequest;

class StoreLectureRequest extends BaseLectureRequest
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
            "lectureFile" => ["required","file", "mimes:pdf,docx,zip","max:20000"], // 20 MB
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'lectureFile' => [
                'description' => 'The lecture file to upload. Allowed types: pdf, docx, zip. Maximum size: 20MB.',
                'required' => true,
                'type' => 'file',
            ],
        ];
    }
}
