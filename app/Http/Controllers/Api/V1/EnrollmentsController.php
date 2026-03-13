<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Resources\V1\EnrollmentResource;
use App\Models\Enrollment;
use Auth;
use Illuminate\Http\Request;

class EnrollmentsController extends ApiController
{
    protected $policyClass = EnrollmentPolicy::class;

    public function store(StoreEnrollmentRequest $request)
    {
        $additionalAttrs = [
            "student_id" => Auth::user()->id,
        ];

        return new EnrollmentResource(Enrollment::create(array_merge($request->mappedAttributes(), $additionalAttrs)));
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

            $enrollment = $this->loadRelationships($toBeIncluded, $enrollment);

            return new EnrollmentResource($enrollment);
        }
        
        return $this->notAuthorized("NOT Authorized");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enrollment $enrollment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        //
    }
}
