<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "comment",
            "id" => $this->id,
            "data" => [
                "content" => $this->content,
                "discussion_id" => $this->discussion_id,
                "user_id" => $this->user_id,
                "createdAt" => $this->created_at,
            ],
            "included" => [
                "user" => new UserResource($this->whenLoaded("user")),
                "discussion" => new DiscussionResource($this->whenLoaded("discussion")),
            ],
            "links" => [
                // TODO
            ]
        ];
    }
}
