<?php

namespace App\Http\Requests\Api\V1\Lectures;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BaseLectureRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = []): array
    {
        $attributeMap = [
                'lectureFile' => 'path',
            ];

        $attributesToUpdate = [];
        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                if ($attribute === 'path') 
                {
                    $file = $this->file($key);
                    $filePath = Storage::disk('public')->put('lectures', $file);

                    $attributesToUpdate[$attribute] = $filePath;
                    continue;
                }
                $value = $this->input($key);

                $attributesToUpdate[$attribute] = $value;
            }
        }

        return array_merge($attributesToUpdate, $otherAttributes);
    }
}
