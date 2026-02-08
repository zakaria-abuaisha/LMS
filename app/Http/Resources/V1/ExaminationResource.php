<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExaminationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "examination",
            "id" => $this->id,
            "data" => [
                "type" => $this->type,
                "grade" => $this->examination,
                "courseId" => $this->course_id,
                "studentId" => $this->student_id,
                "createdAt" => $this->created_at
            ],
            "included" => [
                "course" => new CourseResource($this->whenLoaded("course")),
                "student" => new UserResource($this->whenLoaded("student")),
            ],
            "links" => [
                // TODO :
            ]
        ];
    }
}
