<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "assignment file",
            "id" => $this->id,
            "data" => [
                "path" => $this->path,
                "assignmentId" => $this->assignment_id,
            ],
            "included" => [
                "assignment" => new AssignmentResource($this->whenLoaded("assignment"))
            ],
            "links" => [
                //  TODO
            ]
        ];
    }
}
