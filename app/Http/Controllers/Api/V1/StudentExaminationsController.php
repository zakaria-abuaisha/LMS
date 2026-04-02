<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\ExaminationFilter;
use App\Http\Resources\V1\ExaminationResource;
use App\Models\Course;
use App\Models\Examination;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Auth;

class StudentExaminationsController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    public function index(Course $course, ExaminationFilter $filter)
    {
        if($this->isAble("IsStudentEnrolled", $course))
        {
            return ExaminationResource::collection(
                Examination::where("student_id", Auth::user()->id)
                ->where("course_id", $course->id)
                ->filter($filter)
                ->paginate()
            );
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function show(Examination $examination)
    {
        if($this->isAble("IsStudentEnrolled", $examination->course))
        {
            $examination->unsetRelation("course");
            $toBeLoaded = [
                "course",
                "student",
            ];

            $examination = $this->loadRelationships($examination, $toBeLoaded);
            return new ExaminationResource($examination);
        }
        return $this->notAuthorized("NOT Authorized");
    }
}
