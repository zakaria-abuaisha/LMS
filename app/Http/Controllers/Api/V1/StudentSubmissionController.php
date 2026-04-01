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

    public function show(Submission $submission)
    {
        if ($this->isAble("SubmissionBelongsToStudent", $submission) &&
            $this->isAble("IsStudentEnrolled", $submission))
        {
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

    public function store(Request $request, Assignment $assignment)
    {
        if ($this->isAble("IsStudentEnrolled", $assignment))
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

    public function destroy(Submission $submission)
    {
        if ($this->isAble("SubmissionBelongsToStudent", $submission))
        {
            $filesPaths = $submission->files->pluck("path")->toArray();
            DeleteSubmissionsFiles::dispatch($filesPaths);
            $submission->delete();

            return $this->ok('Submission deleted successfully');
        }
        return $this->notAuthorized("NOT Authorized");
    }
}
