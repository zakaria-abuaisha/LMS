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

    /**
     * Get Submissions
     * 
     * Get all Submissions of a particular course.
     * 
     * @group Manage Submissions For Instructor
     * @queryParam sort string data field(s) to sort by. Seprate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=-submitted_at
     * @queryParam filter[submittedAt] Filter Submissions by submission date, you can also send a comma seprated values that represent (from,to). Example: 2026-4-1,2026-4-7
     * @Response 200 scenario="When you are NOT The instructor of the course." 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 404 
     * {
     *      "errors": [{
     *          "status": 404,
     *          "message": "The Resource Could Not Be Found :("
     *      }]
     * }
     */ 
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

    /**
     * Show a Student Submission.
     * 
     * Display an individual Submission.
     * * available relationships for this resource : 
     *      * assignment : The assignment that the submission belongs to.
     *      * student : The user who submitted the submission called (student).
     *      * files : The files that the student attached with the submission.
     * @group Manage Submissions For Instructor
     * @queryParam include string data field(s) to include any other relationships. Seprate multiple fields with commas. Example: include=student
     * @apiResource App\Http\Resources\V1\SubmissionResource
     * @apiResourceModel App\Models\Submission
     * @Response 200 scenario="When you are NOT The instructor of the course." 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 404 
     * {
     *      "errors": [{
     *          "status": 404,
     *          "message": "The Resource Could Not Be Found :("
     *      }]
     * }
     */
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

    /**
     * Grade a Submission.
     * 
     * Grade a specific Discussion and send an email to the student about the grade.
     * @group Manage Submissions For Instructor
     * @apiResource App\Http\Resources\V1\SubmissionResource
     * @apiResourceModel App\Models\Submission
     * @Response 200 scenario="When you are NOT The instructor of the course." 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 404 
     * {
     *      "errors": [{
     *          "status": 404,
     *          "message": "The Resource Could Not Be Found :("
     *      }]
     * }
     */
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
