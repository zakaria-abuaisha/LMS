<?php

namespace App\Http\Requests\Api\V1\SubmissionFiles;

use App\Http\Requests\Api\V1\SubmissionFiles\BaseSubmissionFileRequest;

class StoreSubmissionFileRequest extends BaseSubmissionFileRequest
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
            "submissionFiles" => ["required","array", "min:1"], 
            "submissionFiles.*" => ["file", "mimes:pdf,jpg,jpeg,png,docx,zip,txt","max:51200"] // 50MB
        ];
    }

    public function bodyParameters():array 
    {
        return [
            'submissionFiles.*' => [
                'type' => "file",
                'description' => 'Uploaded files for a submission. [pdf, jpg, jpeg, png, docx, zip, txt] 50MB.',
                'required' => true, 
            ]
        ];
    }
}
