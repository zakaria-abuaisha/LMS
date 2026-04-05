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
     * Display the specified resource.
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
     * Remove the specified resource from storage.
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
