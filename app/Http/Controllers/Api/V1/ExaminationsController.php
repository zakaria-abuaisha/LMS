<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\ExaminationFilter;
use App\Http\Requests\Api\V1\Examinations\StoreExaminationRequest;
use App\Http\Requests\Api\V1\Examinations\UpdateExaminationRequest;
use App\Http\Resources\V1\ExaminationResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Examination;
use App\Models\User;
use App\Policies\UserPolicy;


class ExaminationsController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    public function index(Course $course, ExaminationFilter $filter)
    {
        if($this->isAble("IsForInstructor", $course))
        {
            return ExaminationResource::collection(
                Examination::where("course_id", "=", $course->id)
                ->filter($filter)
                ->paginate()
            );
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function show(Examination $examination)
    {
        if($this->isAble("IsForInstructor", $examination->course))
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

    public function store(StoreExaminationRequest $request, Course $course, User $user)
    {
        $enrollmentExists = Enrollment::where("course_id", $course->id)
            ->where("student_id", $user->id)
            ->exists();

        if($this->isAble("IsForInstructor", $course) && $enrollmentExists)
        {
            $additionalAttrs = [
                "course_id" => $course->id,
                "student_id" => $user->id,
            ];

            return new ExaminationResource(Examination::create($request->mappedAttributes($additionalAttrs)));
        }
        return $this->notAuthorized("NOT Authorized");
    }
    public function update(UpdateExaminationRequest $request, Examination $examination)
    {
        if($this->isAble("IsForInstructor", $examination->course))
        {
            $examination->unsetRelation("course");

            $examination->update($request->mappedAttributes());
            return new ExaminationResource($examination);
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function destroy(Examination $examination)
    {
        if($this->isAble("IsForInstructor", $examination->course))
        {
            $examination->delete();

            return $this->ok('Examination deleted successfully');
        }
        return $this->notAuthorized("NOT Authorized");
    }
}
