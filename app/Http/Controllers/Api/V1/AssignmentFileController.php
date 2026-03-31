<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\AssignmentFiles\StoreAssignmentFileRequest;
use App\Http\Resources\V1\AssignmentFileResource;
use App\Models\Assignment;
use App\Models\AssignmentFile;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Storage;

class AssignmentFileController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    public function index(Assignment $assignment)
    {
        if ($this->isAble("IsForInstructor", $assignment->course) || $this->isAble("IsStudentEnrolled", $assignment->course))
        {
            return AssignmentFileResource::collection(
                AssignmentFile::where("assignment_id", $assignment->id)->paginate()
            );
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function show(AssignmentFile $assignmentFile)
    {
        if ($this->isAble("IsForInstructor", $assignmentFile->assignment->course) || 
            $this->isAble("IsStudentEnrolled", $assignmentFile->assignment->course))
        {
            $assignmentFile->unsetRelation("assignment");
            $toBeIncluded = [
                "assignment"
            ];

            $assignmentFile = $this->loadRelationships($assignmentFile, $toBeIncluded);

            return new AssignmentFileResource($assignmentFile);
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function store(StoreAssignmentFileRequest $request, Assignment $assignment)
    {
        if ($this->isAble("IsForInstructor", $assignment->course))
        {
            $assignment->unsetRelation("course");

            $assignmentFilesAttrs = $request->mappedAttributes();
            $assignmentFiles = $assignment->files()->createMany($assignmentFilesAttrs);

            return AssignmentFileResource::collection($assignmentFiles);
        }
        return $this->notAuthorized("NOT Authorized");
    }

    public function downloadAssignmentFile(AssignmentFile $assignmentFile)
    {
        if ($this->isAble("IsForInstructor", $assignmentFile->assignment->course) || $this->isAble("IsStudentEnrolled", $assignmentFile->assignment->course))
        {
            $assignmentFile->unsetRelation("assignment");

            $filePath = storage_path("app/public/" . $assignmentFile->path);

            $filename = basename($assignmentFile->path);

            return response()->download($filePath);
        }
        
        return $this->notAuthorized("NOT Authorized");
    }

    public function destroy(AssignmentFile $assignmentFile)
    {
        if ($this->isAble("IsForInstructor", $assignmentFile->assignment->course))
        {
            if(Storage::disk("public")->exists($assignmentFile->path))
                Storage::disk("public")->delete($assignmentFile->path);

            $assignmentFile->delete();
            return $this->ok('Assignment file deleted successfully');
        }
        return $this->notAuthorized("NOT Authorized");
    }
}
