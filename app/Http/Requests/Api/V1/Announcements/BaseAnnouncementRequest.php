<?php

namespace App\Http\Requests\Api\V1\Announcements;

use Illuminate\Foundation\Http\FormRequest;

class BaseAnnouncementRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = []): array
    {
        $attributeMap = [
                'data.attributes.title' => 'title',
                'data.attributes.description' => 'description',
            ];

        $attributesToUpdate = [];
        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                $value = $this->input($key);
                $attributesToUpdate[$attribute] = $value;
            }
        }

        return array_merge($attributesToUpdate, $otherAttributes);
    }
}
