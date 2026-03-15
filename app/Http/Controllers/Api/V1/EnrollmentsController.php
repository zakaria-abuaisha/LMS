<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\Enrollments\StoreEnrollmentRequest as StoreEnrollmentRequest;
use App\Http\Resources\V1\EnrollmentResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Policies\EnrollmentPolicy;
use Illuminate\Support\Facades\Auth as Auth;

class EnrollmentsController extends ApiController
{
    protected $policyClass = EnrollmentPolicy::class;

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
        if ($this->isAble("isBelongsToStudent", $enrollment)) 
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
        if ($this->isAble("isTheInstructor", $enrollment) || $this->isAble("isBelongsToStudent ", $enrollment)) 
        {
            $enrollment->delete();

            return $this->ok("Enrollment Deleted Successfully");
        }

        return $this->notAuthorized("NOT Authorized");
    }
}
