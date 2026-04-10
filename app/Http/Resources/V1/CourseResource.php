<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "type" => "course",
            "id" => $this->id,
            "data" => [
                "courseName" => $this->course_name ,
                "description" => $this->description ,
                "courseCode" => $this->course_code ,
                "startAt" => $this->start_at ,
                "endAt" => $this->end_at ,
                "instructorId" => $this->instructor_id ,
                "assignmentPercent" => $this->assignment_percent ,
                "quizPercent" => $this->quiz_percent ,
                "midPercent" => $this->mid_percent ,
                "finalPercent" => $this->final_percent,
                "createdAt" => $this->created_at,
            ],
            "included" => [
                "instructor" => new UserResource($this->whenLoaded("instructor")),
                "lectures" => LectureResource::collection($this->whenLoaded("lectures")),
                "announcements" => AnnouncementResource::collection($this->whenLoaded("announcements")),
                "discussions" => DiscussionResource::collection($this->whenLoaded("discussions")),
                "enrollments" => EnrollmentResource::collection($this->whenLoaded("enrollments")),
                "examinations" => ExaminationResource::collection($this->whenLoaded("examinations")),
                "assignments" => AssignmentResource::collection($this->whenLoaded("assignments")),
                "students" => UserResource::collection($this->whenLoaded("students")),
            ],
            "links" => [
                // TODO
            ]
        ];
    }
}
