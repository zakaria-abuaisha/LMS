<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\StudentStatisticsResource;
use App\Models\Course;
use App\Models\Examination;
use App\Models\Submission;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Auth;

class StudentStatisticsController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    protected function processStatistics(Course $course) :array
    {
        $user = Auth::user();
        $quizzesPercentage = $course->quiz_percent;
        $assignmentsPercentage = $course->assignment_percent;
        $midPercentage = $course->mid_percent;
        $finalPercentage = $course->final_percent;

        // Assignment Average
        $assignmentsAvg = Submission::where("student_id", $user->id)
            ->whereHas('assignment', function ($query) use ($course) {
                $query->where("course_id", $course->id);
            })
            ->avg("grade");
        
        // Quizzes Average 
        $quizzesAvg = Examination::where("type", "quiz")
            ->where("course_id", $course->id)
            ->where("student_id", $user->id)
            ->avg("grade");

        // Get The Mid Test
        $midTest = Examination::where("type", "mid")
            ->where("course_id", $course->id)
            ->where("student_id", $user->id)
            ->first()
            ->grade;
        
        // Get The Mid Test
        $finalTest = Examination::where("type", "final")
            ->where("course_id", $course->id)
            ->where("student_id", $user->id)
            ->first()
            ->grade;

        $score = ($assignmentsAvg * $assignmentsPercentage / 100) +
            ($quizzesAvg * $quizzesPercentage / 100) +
            ($midTest * $midPercentage / 100) +
            ($finalTest * $finalPercentage / 100);
        
        
        $stats = [
            "average" => $score,
            "mid" => $midTest,
            "final" => $finalTest,
        ];
        return $stats;
    }

    public function __invoke(Course $course)
    {
        if($this->isAble("IsStudentEnrolled", $course))
        {
            $stats = $this->processStatistics($course);

            return new StudentStatisticsResource($stats);
        }
        return $this->notAuthorized("NOT Authorized");
    }
}
