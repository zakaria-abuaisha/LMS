<?php

namespace App\Http\Requests\Api\V1\AssignmentFiles;

use Illuminate\Foundation\Http\FormRequest;

class BaseAssignmentFileRequest extends FormRequest
{
    public function mappedAttributes(): array
    {
        $attributesToStore = [];
        if ($this->hasFile("assignmentFile"))
        {
            $files = $this->file("assignmentFile");
            foreach ($files as $file)
            {
                $originalName = $file->getClientOriginalName();
                $filename = time() . '_' . $originalName;

                $attributesToStore[]["path"] = $file->storeAs('assignmentFiles', $filename, 'public');
            }
        }

        return $attributesToStore;
    }
}
