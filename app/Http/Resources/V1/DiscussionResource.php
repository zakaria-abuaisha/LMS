<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscussionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "discussion",
            "id" => $this->id,
            "data" => [
                "content" => $this->content ,
                "courseId" => $this->course_id ,
                "userId" => $this->user_id,
                "createdAt" => $this->created_at ,
            ],
            "included" => [
                "user" => new UserResource($this->whenLoaded("user")),
                "course" => new CourseResource($this->whenLoaded("course")),
            ],
            "links" => [
                // TODO
            ]
        ];
    }
}
