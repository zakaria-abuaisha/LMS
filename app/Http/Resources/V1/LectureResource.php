<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LectureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "lecture",
            "id" => $this->id,
            "data" => [
                "name" => $this->name,
                "path" => $this->path,
                "courseId" => $this->course_id,
                "createdAt" => $this->created_at,
            ],
            "included" => [
                "course" => new CourseResource($this->whenLoaded("course")),
            ],
            "links" => [
                // TODO :
            ]
        ];
    }
}
