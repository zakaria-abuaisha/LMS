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

    /**
     * Get Files of an Assignment
     * 
     * Get all Files of a particular Assignment.
     * 
     * @group Manage Assignment Files
     * @Response 200 scenario="When you are NOT The instructor of the course, Or NOT enrolled in the course." 
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

    /**
     * Show a specific Assignment File.
     * 
     * Display an individual Submission File.
     * * available relationships for this resource : 
     *      * assignment : The assignment that the file belongs to.
     * @group Manage Assignment Files
     * @queryParam include string data field(s) to include any other relationships. Seprate multiple fields with commas. Example: include=assignment
     * @apiResource App\Http\Resources\V1\AssignmentFileResource
     * @apiResourceModel App\Models\AssignmentFile
     * @Response 200 scenario="When you are NOT The instructor of the course, Or NOT enrolled in the course." 
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

    /**
     * Create an Assignment File.
     * 
     * Create an Assignment File for an assignment.
     * 
     * @group Manage Assignment Files
     * @apiResourceCollection App\Http\Resources\V1\AssignmentFileResource
     * @apiResourceModel App\Models\AssignmentFile
     * @Response 200 scenario="When you are NOT The Instructor of the course" 
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

    /**
     * Download an Assignment File.
     * 
     * Download a file attached to a specific Assignment.
     * This endpoint returns the actual file (PDF, DOCX, ZIP, etc.), not a JSON resource.
     * 
     * @group Manage Assignment Files
     * @responseHeader 200 Content-Type application/octet-stream
     * @responseHeader 200 Content-Disposition attachment; filename="assignment-file.ext"
     * @response 200 scenario="Successful download (binary file response)"
     * @Response 403 scenario="When you are NOT The instructor of the course, Or NOT enrolled in the course."
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
    public function downloadAssignmentFile(AssignmentFile $assignmentFile)
    {
        if ($this->isAble("IsForInstructor", $assignmentFile->assignment->course) || $this->isAble("IsStudentEnrolled", $assignmentFile->assignment->course))
        {
            $assignmentFile->unsetRelation("assignment");

            $filePath = storage_path("app/public/" . $assignmentFile->path);

            return response()->download($filePath);
        }
        
        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Delete an Assignment File.
     * 
     * Delete a specific Assignment File, also from the storage.
     * @group Manage Assignment Files
     * @Response 200 scenario="When you are NOT The instructor of the course."  
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 200 scenario="Successful deletion" 
     * {
     *      "data": [],
     *      "message": "Assignment file deleted successfully",
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
