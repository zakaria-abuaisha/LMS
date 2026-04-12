<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\SubmissionResource;
use App\Jobs\DeleteSubmissionsFiles;
use App\Models\Assignment;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Auth;

class StudentSubmissionController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    /**
     * Show a Student Submission.
     * 
     * Display an individual Submission.
     * * available relationships for this resource : 
     *      * assignment : The assignment that the submission belongs to.
     *      * student : The user who submitted the submission called (student).
     *      * files : The files that the student attached with the submission.
     * @group Manage Student Submissions
     * @queryParam include string data field(s) to include any other relationships. Seprate multiple fields with commas. Example: include=student
     * @apiResource App\Http\Resources\V1\SubmissionResource
     * @apiResourceModel App\Models\Submission
     * @Response 200 scenario="When you are NOT the owner(student) of this submission, Or NOT an enrolled student." 
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
        if ($this->isAble("SubmissionBelongsToStudent", $submission) &&
            $this->isAble("IsStudentEnrolled", $submission->assignment->course))
        {
            $submission->unsetRelation("assignment");
            $toBeIncluded = [
                "assignment",
                "student",
                "files",
            ];

            $submission = $this->loadRelationships($submission, $toBeIncluded);
            return new SubmissionResource($submission);
        }
        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Create a Submission.
     * 
     * Create a Submission by the student, and instantly delete the previous submission for the same assignment if there's one.
     * 
     * @group Manage Student Submissions
     * @apiResource App\Http\Resources\V1\SubmissionResource
     * @apiResourceModel App\Models\Submission
     * @Response 200 scenario="When the user is NOT enrolled in the course." 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 200 scenario="When the deadline date is passed" 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "Sorry, due date has passed!"
     *      }]
     * }
     * @Response 404 
     * {
     *      "errors": [{
     *          "status": 404,
     *          "message": "The Resource Could Not Be Found :("
     *      }]
     * }
     * @Response 200 scenario="Successful submission" 
     * {
     *      "data": [],
     *      "message": "The assignment submitted successfully.",
     *      "code": 200
     * }
     */
    public function store(Request $request, Assignment $assignment)
    {
        if ($this->isAble("IsStudentEnrolled", $assignment->course))
        {
            if($this->isAble("BeforeDueDate", $assignment))
            {
                $user = Auth::user(); 
                $oldSubmission = Submission::where("assignment_id", "=", $assignment->id)
                    ->where("student_id", "=", Auth::user()->id)
                    ->first();

                if ($oldSubmission)
                {
                    $filesPaths = $oldSubmission->files->pluck("path")->toArray();
                    DeleteSubmissionsFiles::dispatch($filesPaths);
                    $oldSubmission->delete();
                }

                $submission = Submission::create([
                    "assignment_id" => $assignment->id,
                    "student_id" => $user->id,
                ]);

                return $this->ok("The assignment submitted successfully.");
            }

            return $this->notAuthorized("Sorry, due date has passed!");
        }

        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Delete a Submission.
     * 
     * Delete a specific Submission with the attached files.
     * @group Manage Student Submissions
     * @Response 200 scenario="When you are NOT the owner(student) of this submission, Or NOT an enrolled student."  
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 200 scenario="Successful deletion" 
     * {
     *      "data": [],
     *      "message": "Submission deleted successfully",
     *      "code": 200
     * }
     * @Response 404 
     * {
     *      "errors": [{
     *          "status": 404,
     *          "message": "The Resource Could Not Be Found :("
     *      }]
     * }
     */
    public function destroy(Submission $submission)
    {
        if ($this->isAble("SubmissionBelongsToStudent", $submission) || 
            $this->isAble("IsStudentEnrolled", $submission->assignment->course))
        {
            $filesPaths = $submission->files->pluck("path")->toArray();
            DeleteSubmissionsFiles::dispatch($filesPaths);
            $submission->delete();

            return $this->ok('Submission deleted successfully');
        }
        return $this->notAuthorized("NOT Authorized");
    }
}
