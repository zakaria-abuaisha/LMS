<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "user",
            "id" => $this->id,
            "data" => [
                "firstName" => $this->first_name,
                "lastName" => $this->last_name,
                "email" => $this->email,
                "createdAt" => $this->created_at
            ],
            "included" => [
                "createdCourses" => CourseResource::collection($this->whenLoaded("courses")),
                "enrolledCourses" => CourseResource::collection($this->whenLoaded("enrolledCourses")),
                "enrollments" => EnrollmentResource::collection($this->whenLoaded("enrollments")),
                "examinations" => ExaminationResource::collection($this->whenLoaded("examinations")),
                "submissions" => SubmissionResource::collection($this->whenLoaded("submissions")),
                "discussions" => DiscussionResource::collection($this->whenLoaded("discussions")),
                "comments" => CommentResource::collection($this->whenLoaded("comments")),
            ],
            "links" => [
                // TODO : 
            ]
        ];
    }
}
