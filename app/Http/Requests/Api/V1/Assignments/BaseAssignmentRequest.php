<?php

namespace App\Http\Requests\Api\V1\Assignments;

use Illuminate\Foundation\Http\FormRequest;

class BaseAssignmentRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = []): array
    {
        $attributeMap = [
                'data.attributes.subject' => 'subject',
                'data.attributes.content' => 'content',
                'data.attributes.dueDate' => 'due_date',
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
