<?php

namespace App\Http\Requests\Api\V1\Comments;

use App\Http\Requests\Api\V1\Lectures\BaseLectureRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends BaseLectureRequest
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
            "content" => ["sometimes", "string","max:2500"], // 20 MB
        ];
    }
}
