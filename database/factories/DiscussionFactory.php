<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscussionFactory extends Factory
{
    public function definition(): array
    {
        $course = Course::inRandomOrder()->first()
            ?? Course::factory()->create();

        $userId = Enrollment::where('course_id', $course->id)
            ->inRandomOrder()
            ->value('student_id');
        
        if (!$userId) {
            $student = User::factory()->create();

            Enrollment::create([
                'course_id' => $course->id,
                'student_id' => $student->id,
            ]);

            $userId = $student->id;
        }
        
        return [
            'course_id' => $course->id,
            'user_id' => $userId,
            'content' => fake()->sentence(10),
        ];
    }
}