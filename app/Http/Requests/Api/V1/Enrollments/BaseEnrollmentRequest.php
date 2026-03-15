<?php

namespace App\Http\Requests\Api\V1\Enrollments;

use Illuminate\Foundation\Http\FormRequest;

class BaseEnrollmentRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = []): array
    {
        $attributeMap = array_merge(
            [
                'data.attributes.courseCode' => 'course_code',
            ]
        , $otherAttributes);

        $attributesToUpdate = [];
        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                $value = $this->input($key);
                $attributesToUpdate[$attribute] = $value;
            }
        }

        return $attributesToUpdate;
    }
}
