<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\Lectures\StoreLectureRequest;
use App\Http\Resources\V1\LectureResource;
use App\Models\Course;
use App\Models\Lecture;
use App\Policies\CoursePolicy;
use Illuminate\Support\Facades\Storage;

class LecturesController extends ApiController
{
    protected $policyClass = CoursePolicy::class;

    public function index(Course $course)
    {
        if ($this->authorize("IsForInstructor", $course) || $this->authorize("IsStudentEnrolled", $course)) 
        {
               return LectureResource::collection(Lecture::where("course_id", $course->id)->paginate());
        }

        return $this->notAuthorized("NOT Authorized");
    }

    public function store(StoreLectureRequest $request, Course $course)
    {
        if ($this->authorize("IsForInstructor", $course))
        {
            $fileName = $request->file("lectureFile")->getClientOriginalName();
            
            $additionalAttributes = [
                "course_id" => $course->id,
                "name" => $fileName
            ];

            return new LectureResource(Lecture::create($request->mappedAttributes($additionalAttributes)));
        }

        return $this->notAuthorized("NOT Authorized");
    }   

    public function show(Lecture $lecture)
    {
        if ($this->authorize("IsForInstructor", $lecture->course) || $this->isAble("IsStudentEnrolled", $lecture->course))
        {
            $lecture->unsetRelation("course");
            $toIncluded = [
                'course'
            ];

            $lecture = $this->loadRelationships($lecture, $toIncluded);

            return new LectureResource($lecture);
        }

        return $this->notAuthorized("NOT Authorized");
    }

    public function downloadLectureFile(Lecture $lecture)
    {
        if ($this->authorize("IsForInstructor", $lecture->course) || $this->isAble("IsStudentEnrolled", $lecture->course))
        {
            $lecture->unsetRelation("course");
            return Storage::disk('public')->download($lecture->path);
        }
        
        return $this->notAuthorized("NOT Authorized");
    }

    public function destroy(Lecture $lecture)
    {
        if ($this->authorize("IsForInstructor", $lecture->course) || $this->isAble("IsStudentEnrolled", $lecture->course))
        {
            if (Storage::disk("public")->exists($lecture->path))
                Storage::disk("public")->delete($lecture->path);

            $lecture->delete();

            return $this->ok('Lecture deleted successfully');
        }

        return $this->notAuthorized("NOT Authorized");
    }

}
