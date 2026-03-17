<?php

namespace App\Http\Requests\Api\V1\Lectures;

use App\Http\Requests\Api\V1\Lectures\BaseLectureRequest;
use Illuminate\Foundation\Http\FormRequest;

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
            "lectureFile" => ["required","mimes:pdf,docx,zip","max:20000"], // 20 MB
        ];
    }
}
