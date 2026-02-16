<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "announcement",
            "id"=> $this->resource->id,
            "data" => [
                "title" => $this->title,
                "description" => $this->description,
                "courseId" => $this->course_id,
                "createdAt" => $this->created_at
            ],
            "included" => [
                "course" => new CourseResource($this->whenLoaded("course")),
            ],
            "links" => [
                //  TODO
            ]
        ];
    }
}
