<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\Enrollments\StoreEnrollmentRequest as StoreEnrollmentRequest;
use App\Http\Resources\V1\EnrollmentResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Auth as Auth;

class EnrollmentsController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    /**
     * Enroll a student(User)
     * 
     * Enroll a student into a course via course code.
     * @group Manage Enrollments
     * @apiResource App\Http\Resources\V1\EnrollmentResource
     * @apiResourceModel App\Models\Enrollment
     * @Response 200 scenario="When There's NO course with the submitted course code."
     * {
     *      "errors": [{
     *          "status": 422,
     *          "message": "The selected data.attributes.course code is invalid.",
     *          "source": "data.attributes.courseCode"
     *      }]
     * }
     * @Response 200 scenario="When a User attempts to enroll again"
     * {
     *      "errors": [{
     *          "status": 422,
     *          "message": "You're Already Enrolled!",
     *          "source": "data.attributes.courseCode"
     *      }]
     * }
     * @Response 200 scenario="When The Instructor attempts to enroll into his/her own course."
     * {
     *      "errors": [{
     *          "status": 422,
     *          "message": "You are the Instructor, you can not be a student for this course!",
     *          "source": "data.attributes.courseCode"
     *      }]
     * }
     */
    public function store(StoreEnrollmentRequest $request)
    {
        $courseCode = $request->mappedAttributes();
        $additionalAttrs = [
            "student_id" => Auth::user()->id,
            "course_id" => Course::select("id")->where("course_code", $courseCode)->first()->id
        ];

        return new EnrollmentResource(Enrollment::create($additionalAttrs));
    }

    /**
     * Show a specific enrollment.
     * 
     * Display an individual enrollment for the student.
     * * available relationships for this resourc : 
     *      * course : The course that the Enrollment belongs to.
     *      * student : The student that the Enrollment belongs to.
     * @group Manage Enrollments
     * @queryParam include string data field(s) to include any other relationships. Seprate multiple fields with commas. Example: include=course,student
     * @apiResource App\Http\Resources\V1\EnrollmentResource
     * @apiResourceModel App\Models\Enrollment
     * @Response 200 scenario="When you are NOT the student who belongs to the Enrollment." 
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
    public function show(Enrollment $enrollment)
    {
        if ($this->isAble("IsEnrollmentBelongsToStudent", $enrollment)) 
        {
            $toBeIncluded = [
                "student",
                "course"
            ];

            $enrollment = $this->loadRelationships($enrollment, $toBeIncluded);

            return new EnrollmentResource($enrollment);
        }
        
        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Delete a specific enrollment.
     * 
     * Delete an individual enrollment for the student.
     * @group Manage Enrollments
     * @Response 200 scenario="When you are NOT the student who belongs to the Enrollment." 
     * {
     *      "errors": [{
     *          "status": 401,
     *          "message": "NOT Authorized"
     *      }]
     * }
     * @Response 200 scenario="Successful deletion" 
     * {
     *      "data": [],
     *      "message": "Enrollment deleted successfully",
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
    public function destroy(Enrollment $enrollment)
    {
        if ($this->isAble("IsForInstructor", $enrollment->course) || $this->isAble("IsEnrollmentBelongsToStudent ", $enrollment)) 
        {
            $enrollment->delete();

            return $this->ok("Enrollment Deleted Successfully");
        }

        return $this->notAuthorized("NOT Authorized");
    }
}
