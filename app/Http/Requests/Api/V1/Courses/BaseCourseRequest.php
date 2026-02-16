<?php

namespace App\Http\Requests\Api\V1\Courses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class BaseCourseRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = []): array
    {
        $attributeMap = array_merge(
            [
                'data.attributes.courseName' => 'course_name',
                'data.attributes.description' => 'description',
                'data.attributes.startAt' => 'start_at',
                'data.attributes.endAt' => 'end_at',
                'data.attributes.assignmentPercent' => 'assignment_percent',
                'data.attributes.quizPercent' => 'quiz_percent',
                'data.attributes.midPercent' => 'mid_percent',
                'data.attributes.finalPercent' => 'final_percent'
            ]
        , $otherAttributes);

        $attributesToUpdate = [];
        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                $value = $this->input($key);

                if($attribute === 'password')
                {
                    $value = bcrypt($value);
                }
                $attributesToUpdate[$attribute] = $value;
            }
        }

        return $attributesToUpdate;
    }
}
