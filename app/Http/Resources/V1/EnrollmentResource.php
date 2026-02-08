<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "enrollment",
            "id" => $this->id,
            "data" => [
                "enrollDate" => $this->enroll_date,
                "courseId" => $this->course_id,,
                "studentId" => $this->student_id,,
            ],
            "included" => [
                "student" => new UserResource($this->whenLoaded("student")),
                "course" => new CourseResource($this->whenLoaded("course")),
            ],
            "links" => [
                // TODO
            ]
        ];
    }
}
