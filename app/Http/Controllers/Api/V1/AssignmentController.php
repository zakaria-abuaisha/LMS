<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AssignmentFilter;
use App\Http\Requests\Api\V1\Assignments\StoreAssignmentRequest;
use App\Http\Requests\Api\V1\Assignments\UpdateAssignmentRequest;
use App\Http\Resources\V1\AssignmentResource;
use App\Models\Assignment;
use App\Models\Course;
use App\Policies\UserPolicy;

class AssignmentController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    public function index(Course $course, AssignmentFilter $filter)
    {
        if ($this->isAble("IsForInstructor", $course) || $this->isAble("IsStudentEnrolled", $course))
        {
            return AssignmentResource::collection(
                Assignment::where("course_id", $course->id)
                    ->filter($filter)
                    ->paginate()
            );
        }

        return $this->notAuthorized("NOT Authorized");
    }

    public function show(Assignment $assignment)
    {
        if ($this->isAble("IsForInstructor", $assignment->course) 
            || $this->isAble("IsStudentEnrolled", $assignment->course))
        {
            $assignment->unsetRelation("course");

            $toBeIncluded = [
                "course",
                "files",
                "Submissions",
            ];

            $assignment = $this->loadRelationships($assignment, $toBeIncluded);
            
            return new AssignmentResource($assignment);
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function store(StoreAssignmentRequest $request, Course $course)
    {
        if ($this->isAble("IsForInstructor", $course))
        {
            $additionalAttributes = [
                "course_id" => $course->id,
            ];

            return new AssignmentResource(
                Assignment::create($request->mappedAttributes($additionalAttributes))
            );
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        if ($this->isAble("IsForInstructor", $assignment->course))
        {
            $assignment->unsetRelation("course");
            $assignment->update($request->mappedAttributes);

            return new AssignmentResource($assignment);
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function destroy(Assignment $assignment)
    {
        if ($this->isAble("IsForInstructor", $assignment->course))
        {
            $assignment->delete();

            return $this->ok('Assignment deleted successfully');
        }
        return $this->notAuthorized("NOT Authorized");
    }
}
