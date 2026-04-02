<?php

namespace App\Http\Requests\Api\V1\SubmissionFiles;

use Illuminate\Foundation\Http\FormRequest;

class BaseSubmissionFileRequest extends FormRequest
{
    public function mappedAttributes(): array
    {
        $attributesToStore = [];
        if ($this->hasFile("submissionFiles"))
        {
            $files = $this->file("submissionFiles");
            foreach ($files as $file)
            {
                $originalName = $file->getClientOriginalName();
                $filename = time() . '_' . $originalName;

                $attributesToStore[]["path"] = $file->storeAs('submissionFiles', $filename, 'public');
            }
        }

        return $attributesToStore;
    }
}
