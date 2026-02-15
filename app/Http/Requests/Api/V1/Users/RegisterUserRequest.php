<?php

namespace App\Http\Requests\Api\V1\Users;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            "firstName" => ["required","string", "min:3", "max:100"],
            "lastName" => ["required","string", "min:3", "max:100"],
            "email"=> ["required","string","email", "unique:users"],
            "password"=> ["required", "string", "min:6" ,"confirmed"],
        ];
    }
}
