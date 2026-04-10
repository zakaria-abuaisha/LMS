<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\ExaminationFilter;
use App\Http\Resources\V1\ExaminationResource;
use App\Models\Course;
use App\Models\Examination;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Auth;

class StudentExaminationsController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    /**
     * Get Student Examinations
     * 
     * Get all examinations of a particular course for a student in a particular course.
     * 
     * @group Student Examinations
     * @queryParam sort string data field(s) to sort by. Seprate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=grade,-created_at
     * @queryParam filter[type] Filter examinations by it's type. [quiz, mid, final].Example: *Machine Learning*
     * @queryParam filter[cratedAt] Filter examinations by cratedAt date, you cam also send a comma seprated values that represent (from,to). Example: 2026-4-1,2026-4-7
     * @Response 200 scenario="When you are NOT Enrolled in the course." 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     */ 
    public function index(Course $course, ExaminationFilter $filter)
    {
        if($this->isAble("IsStudentEnrolled", $course))
        {
            return ExaminationResource::collection(
                Examination::where("student_id", Auth::user()->id)
                ->where("course_id", $course->id)
                ->filter($filter)
                ->paginate()
            );
        }
        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Show a Student Examination.
     * 
     * Display an individual examination for the student.
     * * available relationships for this resource : 
     *      * course : The course that the examination belongs to.
     *      * student : The student that the examination belongs to.
     * @group Student Examinations
     * @queryParam include string data field(s) to include any other relationships. Seprate multiple fields with commas. Example: include=course,student
     * @apiResource App\Http\Resources\V1\ExaminationResource
     * @apiResourceModel App\Models\Examination
     * @Response 200 scenario="When you are NOT Enrolled in the course." 
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
        if($this->isAble("IsStudentEnrolled", $examination->course))
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
}
