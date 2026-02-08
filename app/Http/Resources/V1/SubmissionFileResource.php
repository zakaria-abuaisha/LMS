<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "submission file",
            "id" => $this->id,
            "data" => [
                "path" => $this->path,
                "submissionId" => $this->submissionId
            ],
            "included" => [
                "submission" => new SubmissionResource($this->whenLoaded("submission")),
            ],
            "links" => [
                // TODO :
            ]
        ];
    }
}
