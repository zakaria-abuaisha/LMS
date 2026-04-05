<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    public function forCourse($courseId)
    {
        return $this->state(fn () => ['course_id' => $courseId]);
    }

    public function forStudent($studentId)
    {
        return $this->state(fn () => ['student_id' => $studentId]);
    }    

    public function definition(): array
    {
        $course = Course::inRandomOrder()->first()
                ?? Course::factory()->create();

        $start = $course->start_at;
        $end = $course->end_at ?? now();
        return [
            'course_id' => $course->id,
            'student_id' => User::inRandomOrder()->value('id')
                ?? User::factory(),
            'enroll_date' => fake()->dateTimeBetween($start, $end),
        ];
    }
}