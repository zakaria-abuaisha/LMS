<?php

namespace App\Http\Requests\Api\V1\AssignmentFiles;

use App\Http\Requests\Api\V1\AssignmentFiles\BaseAssignmentFileRequest;

class StoreAssignmentFileRequest extends BaseAssignmentFileRequest
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
            "assignmentFile" => ["required","array", "min:1"], 
            "assignmentFile.*" => ["file", "mimes:pdf,jpg,jpeg,png,docx,zip,txt","max:51200"] // 50MB
        ];
    }

    public function bodyParameters():array 
    {
        return [
            'assignmentFile.*' => [
                'type' => "file",
                'description' => 'Uploaded files with an assignment. [pdf, jpg,jpeg, png, docx, zip, txt] 50MB.',
                'required' => true, 
            ]
        ];
    }
}
