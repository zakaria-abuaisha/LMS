<?php

namespace App\Http\Requests\Api\V1\Discussions;

use Illuminate\Foundation\Http\FormRequest;

class BaseDiscussionsRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = []): array
    {
        $attributeMap = [
                'data.attributes.content' => 'content',
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
