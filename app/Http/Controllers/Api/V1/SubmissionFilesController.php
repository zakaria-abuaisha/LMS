<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\SubmissionFiles\StoreSubmissionFileRequest;
use App\Http\Resources\V1\SubmissionFileResource;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Storage;

class SubmissionFilesController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    public function index(Submission $submission)
    {
        if($this->isAble("IsForInstructor", $submission->assignment->course) ||
            $this->isAble("SubmissionBelongsToStudent", $submission)
        )
        {
            return SubmissionFileResource::collection(
                SubmissionFile::where("submission_id", "=", $submission->id)
                ->paginate()
            );
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function show(SubmissionFile $submissionFile)
    {
        if($this->isAble("IsForInstructor", $submissionFile->submission->assignment->course) ||
            $this->isAble("SubmissionBelongsToStudent", $submissionFile->submission))
        {
            $submissionFile->unsetRelation("submission");
            $toBeIncluded = [
                "submission"
            ];

            $submissionFile = $this->loadRelationships($submissionFile, $toBeIncluded);

            return new SubmissionFileResource($submissionFile);
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function store(StoreSubmissionFileRequest $request, Submission $submission)
    {
        if($this->isAble("SubmissionBelongsToStudent", $submission->assignment->course))
        {
            $submission->unsetRelation("assignment");

            $submissionFilesAttrs = $request->mappedAttributes();
            $submissionFiles = $submission->files()->createMany($submissionFilesAttrs);

            return SubmissionFileResource::collection(
                $submissionFiles
            );
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function downloadSubmissionFile(SubmissionFile $submissionFile)
    {
        if ($this->isAble("IsForInstructor", $submissionFile->submission->assignment->course) || 
            $this->isAble("SubmissionBelongsToStudent", $submissionFile->submission))
        {
            $submissionFile->unsetRelation("submission");

            $filePath = storage_path("app/public/" . $submissionFile->path);

            return response()->download($filePath);
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function destroy(SubmissionFile $submissionFile)
    {
        if($this->isAble("SubmissionBelongsToStudent", $submissionFile->submission))
        {
            if(Storage::disk("public")->exists($submissionFile->path))
                Storage::disk("public")->delete($submissionFile->path);

            $submissionFile->delete();
            return $this->ok('Submission file deleted successfully');
        }
    }

}
