<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\SubmissionFilter;
use App\Http\Requests\Api\V1\GradeSubmissions\UpdateGradeSubmissionRequest;
use App\Http\Resources\V1\SubmissionResource;
use App\Jobs\PropagateSubmissionGrade;
use App\Models\Assignment;
use App\Models\Submission;
use App\Policies\UserPolicy;

class SubmissionsController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    public function index(Assignment $assignment, SubmissionFilter $filter)
    {
        if ($this->isAble("IsForInstructor", $assignment->course))
        {
            return SubmissionResource::collection(
                Submission::where("assignment_id","=", $assignment->id)
                ->filter($filter)
                ->paginate()
            );
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function show(Submission $submission)
    {
        if($this->isAble("IsForInstructor", $submission->assignment->course))
        {
            $submission->unsetRelation("assignment");

            $toBeIncluded = [
                "assignment",
                "student",
                "files"
            ];

            $submission = $this->loadRelationships($submission, $toBeIncluded);

            return new SubmissionResource(
                $submission
            );
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function grade(UpdateGradeSubmissionRequest $request, Submission $submission)
    {
        if($this->isAble("IsForInstructor", $submission->assignment->course))
        {
            $submission->unsetRelation("assignment");

            $submission->update(
                [
                    "grade" => $request->input("data.attributes.grade")
                ]
            );

            PropagateSubmissionGrade::dispatch($submission);

            return new SubmissionResource(
                $submission
            );
        }
        return $this->notAuthorized("NOT Authorized");
    }
}
