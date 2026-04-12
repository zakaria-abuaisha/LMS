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

    /**
     * Get Files of a Submission
     * 
     * Get all Files of a particular Submission.
     * 
     * @group Manage Submission Files
     * @Response 200 scenario="When you are NOT The instructor of the course, Or NOT the owner(student) of the submission." 
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

    /**
     * Show a specific Submission File.
     * 
     * Display an individual Submission File.
     * * available relationships for this resource : 
     *      * submission : The submission that the file belongs to.
     * @group Manage Submission Files
     * @queryParam include string data field(s) to include any other relationships. Seprate multiple fields with commas. Example: include=course
     * @apiResource App\Http\Resources\V1\SubmissionFileResource
     * @apiResourceModel App\Models\SubmissionFile
     * @Response 200 scenario="When you are NOT The instructor of the course, Or NOT the owner(student) of the submission." 
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

    /**
     * Create a Submission File.
     * 
     * Create a Submission File by a student.
     * 
     * @group Manage Submission Files
     * @apiResourceCollection App\Http\Resources\V1\SubmissionFileResource
     * @apiResourceModel App\Models\SubmissionFile
     * @Response 200 scenario="When you are NOT The owner(student) of the submission" 
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
    public function store(StoreSubmissionFileRequest $request, Submission $submission)
    {
        if($this->isAble("SubmissionBelongsToStudent", $submission))
        {
            $submissionFilesAttrs = $request->mappedAttributes();
            $submissionFiles = $submission->files()->createMany($submissionFilesAttrs);

            return SubmissionFileResource::collection(
                $submissionFiles
            );
        }
        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Download a Submission File.
     * 
     * Download a file attached to a specific submission.
     * This endpoint returns the actual file (PDF, DOCX, ZIP, etc.), not a JSON resource.
     * 
     * @group Manage Submission Files
     * @responseHeader 200 Content-Type application/octet-stream
     * @responseHeader 200 Content-Disposition attachment; filename="submission-file.ext"
     * @response 200 scenario="Successful download (binary file response)"
     * @Response 403 scenario="When you are NOT the instructor of the course, Or NOT the owner(student) of the submission."
     * {
     *      "errors": [{
     *          "status": 403,
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

    /**
     * Delete a Submission File.
     * 
     * Delete a specific Submission File, also from the storage.
     * @group Manage Submission Files
     * @Response 200 scenario="When you're not the owner(student) of the Submission File."  
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 200 scenario="Successful deletion" 
     * {
     *      "data": [],
     *      "message": "Submission file deleted successfully",
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
    public function destroy(SubmissionFile $submissionFile)
    {
        if($this->isAble("SubmissionBelongsToStudent", $submissionFile->submission))
        {
            if(Storage::disk("public")->exists($submissionFile->path))
                Storage::disk("public")->delete($submissionFile->path);

            $submissionFile->delete();
            return $this->ok('Submission file deleted successfully');
        }
        return $this->notAuthorized("NOT Authorized");
    }

}
