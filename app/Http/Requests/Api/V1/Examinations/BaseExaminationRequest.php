<?php

namespace App\Http\Requests\Api\V1\Examinations;

use Illuminate\Foundation\Http\FormRequest;

class BaseExaminationRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = []): array
    {
        $attributeMap = [
                'data.attributes.type' => 'type',
                'data.attributes.grade' => 'type',
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
