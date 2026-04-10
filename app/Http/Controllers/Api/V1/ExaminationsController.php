<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\ExaminationFilter;
use App\Http\Requests\Api\V1\Examinations\StoreExaminationRequest;
use App\Http\Requests\Api\V1\Examinations\UpdateExaminationRequest;
use App\Http\Resources\V1\ExaminationResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Examination;
use App\Models\User;
use App\Policies\UserPolicy;


class ExaminationsController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    /**
     * Get Examinations
     * 
     * Get all examinations of a particular course for the instructor.
     * 
     * @group Manage Examinations
     * @queryParam sort string data field(s) to sort by. Seprate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=grade,-created_at
     * @queryParam filter[type] Filter examinations by it's type. [quiz, mid, final].Example: *Machine Learning*
     * @queryParam filter[cratedAt] Filter examinations by creation date, you cam also send a comma seprated values that represent (from,to). Example: 2026-4-1,2026-4-7
     * @Response 200 scenario="When you are NOT The instructor of the course." 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     */ 
    public function index(Course $course, ExaminationFilter $filter)
    {
        if($this->isAble("IsForInstructor", $course))
        {
            return ExaminationResource::collection(
                Examination::where("course_id", "=", $course->id)
                ->filter($filter)
                ->paginate()
            );
        }
        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Show a specific Examination.
     * 
     * Display an individual examination for the instructor.
     * * available relationships for this resource : 
     *      * course : The course that the examination belongs to.
     *      * student : The student that the examination belongs to.
     * @group Manage Examinations
     * @queryParam include string data field(s) to include any other relationships. Seprate multiple fields with commas. Example: include=course,student
     * @apiResource App\Http\Resources\V1\ExaminationResource
     * @apiResourceModel App\Models\Examination
     * @Response 200 scenario="When you are NOT the instructor of the course." 
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
    public function show(Examination $examination)
    {
        if($this->isAble("IsForInstructor", $examination->course))
        {
            $examination->unsetRelation("course");
            $toBeLoaded = [
                "course",
                "student",
            ];

            $examination = $this->loadRelationships($examination, $toBeLoaded);
            return new ExaminationResource($examination);
        }
        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Create an Examination.
     * 
     * Create an examination by the instructor for a student in a particular course.
     * @group Manage Examinations
     * @apiResource App\Http\Resources\V1\ExaminationResource
     * @apiResourceModel App\Models\Examination
     * @Response 200 scenario="When you are NOT the instructor of the course OR the student is not even enrolled." 
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
    public function store(StoreExaminationRequest $request, Course $course, User $user)
    {
        $enrollmentExists = Enrollment::where("course_id", $course->id)
            ->where("student_id", $user->id)
            ->exists();

        if($this->isAble("IsForInstructor", $course) && $enrollmentExists)
        {
            $additionalAttrs = [
                "course_id" => $course->id,
                "student_id" => $user->id,
            ];

            return new ExaminationResource(Examination::create($request->mappedAttributes($additionalAttrs)));
        }
        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Update an Examination.
     * 
     * Update a specific examination by the instructor.
     * @group Manage Examinations
     * @apiResource App\Http\Resources\V1\ExaminationResource
     * @apiResourceModel App\Models\Examination
     * @Response 200 scenario="When you are NOT the instructor of the course." 
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
    public function update(UpdateExaminationRequest $request, Examination $examination)
    {
        if($this->isAble("IsForInstructor", $examination->course))
        {
            $examination->unsetRelation("course");

            $examination->update($request->mappedAttributes());
            return new ExaminationResource($examination);
        }
        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Delete an Examinations.
     * 
     * Delete a specific examination by the instructor.
     * @group Manage Examinations
     * @Response 200 scenario="When you are NOT the instructor of the course." 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 200 scenario="Successful deletion" 
     * {
     *      "data": [],
     *      "message": "Examination deleted successfully",
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
    public function destroy(Examination $examination)
    {
        if($this->isAble("IsForInstructor", $examination->course))
        {
            $examination->delete();

            return $this->ok('Examination deleted successfully');
        }
        return $this->notAuthorized("NOT Authorized");
    }
}
