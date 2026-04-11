<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AssignmentFilter;
use App\Http\Requests\Api\V1\Assignments\StoreAssignmentRequest;
use App\Http\Requests\Api\V1\Assignments\UpdateAssignmentRequest;
use App\Http\Resources\V1\AssignmentResource;
use App\Jobs\DeleteAssignmentFiles;
use App\Jobs\PropagateAssignment;
use App\Models\Assignment;
use App\Models\Course;
use App\Policies\UserPolicy;

class AssignmentController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    /**
     * Get Assignments
     * 
     * Get all Assignments of a particular course.
     * 
     * @group Manage Assignments
     * @queryParam sort string data field(s) to sort by. Seprate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=-created_at,subject,due_date
     * @queryParam filter[subject] Filter Assignments by the subject. Example: *Assignment-3*
     * @queryParam filter[createdAt] Filter Assignments by creation date date, you cam also send a comma seprated values that represent (from,to). Example: 2026-4-1,2026-4-7
     * @queryParam filter[dueDate] Filter Assignments by due date date, you cam also send a comma seprated values that represent (from,to). Example: 2026-4-1,2026-4-7
     * @Response 200 scenario="When you are NOT The instructor of the course, Or NOT an enrolled student." 
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

    /**
     * Show a specific Assignment.
     * 
     * Display an individual Assignment for a particular course.
     * * available relationships for this resource : 
     *      * course : The course that the Assignment belongs to.
     *      * files : The files attached with this assignment.
     *      * Submissions : The submissions of an assignment by the enrolled students.
     * @group Manage Assignments
     * @queryParam include string data field(s) to include any other relationships. Seprate multiple fields with commas. Example: include=course
     * @apiResource App\Http\Resources\V1\AssignmentResource
     * @apiResourceModel App\Models\Assignment
     * @Response 200 scenario="When you are NOT the instructor of the course, Or NOT even an enrolled student." 
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

    /**
     * Create an Assignment.
     * 
     * Create an Assignment by the instructor in a particular course.
     * Note: An Email will be sent for all enrolled students once an Assignment is published.
     * @group Manage Assignments
     * @apiResource App\Http\Resources\V1\AssignmentResource
     * @apiResourceModel App\Models\Assignment
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
     * @Response 200 scenario="When The content field exceeds 2500 characters" 
     * {
     *    "errors": [
     *        {
     *            "status": 422,
     *            "message": "The data.attributes.content field must not be greater than 2500 characters.",
     *            "source": "data.attributes.content"
     *       }
     *    ]
     *}
     */
    public function store(StoreAssignmentRequest $request, Course $course)
    {
        if ($this->isAble("IsForInstructor", $course))
        {
            $additionalAttributes = [
                "course_id" => $course->id,
            ];

            $assignment = Assignment::create($request->mappedAttributes($additionalAttributes));

            PropagateAssignment::dispatch($course, $assignment);

            return new AssignmentResource(
                $assignment
            );
        }
        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Update an Assignment.
     * 
     * Update a specific Assignment.
     * @group Manage Assignments
     * @apiResource App\Http\Resources\V1\AssignmentResource
     * @apiResourceModel App\Models\Assignment
     * @Response 200 scenario="When you're not the instructor of the course" 
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
     * @Response 200 cenario="When The content field exceeds 2500 characters" 
     * {
     *    "errors": [
     *        {
     *            "status": 422,
     *            "message": "The data.attributes.content field must not be greater than 2500 characters.",
     *            "source": "data.attributes.content"
     *       }
     *    ]
     *}
     */
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

    /**
     * Delete n Assignment.
     * 
     * Delete a specific Assignment, also Deletes assignment files from the storage.
     * @group Manage Assignments
     * @Response 200 scenario="When you're not the instructor."  
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 200 scenario="Successful deletion" 
     * {
     *      "data": [],
     *      "message": "Assignment deleted successfully",
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
    public function destroy(Assignment $assignment)
    {
        if ($this->isAble("IsForInstructor", $assignment->course))
        {
            $filesPaths = $assignment->files->pluck("path")->toArray();
            DeleteAssignmentFiles::dispatch($filesPaths);
            $assignment->delete();

            return $this->ok('Assignment deleted successfully');
        }
        return $this->notAuthorized("NOT Authorized");
    }
}
