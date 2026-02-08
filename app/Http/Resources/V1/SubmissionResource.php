<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "submission",
            "id" => $this->id,
            "data" => [
                "grade" => $this->grade,
                "submittedAt" => $this->submitted_at,
                "assignmentId" => $this->assignment_id,
                "student_id" => $this->student_id,
            ],
            "included" => [
                "assignment" => new AssignmentResource($this->whenLoaded("assignment")),
                "student" => new UserResource($this->whenLoaded("student")),
                "files" => AssignmentFileResource::collection($this->whenLoaded("files")),
            ],
            "links" => [
                // TODO : 
            ]
        ];
    }
}
