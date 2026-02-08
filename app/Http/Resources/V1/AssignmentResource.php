<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "assignment",
            "id" => $this->id,
            "data" => [
                "subject" => $this->subject,
                "content" => $this->content,
                "courseId" => $this->course_id,
                "dueDate" => $this->due_date,
            ],
            "included" => [
                "course" => new CourseResource($this->whenLoaded("course")),
                "files" => AssignmentFileResource::collection($this->whenLoaded("files")),
                "Submissions" => SubmissionResource::collection($this->whenLoaded("Submissions")),
            ],
            "links" => [
                // TODO
            ]
        ];
    }
}
