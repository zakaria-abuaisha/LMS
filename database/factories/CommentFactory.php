<?php

namespace Database\Factories;

use App\Models\Discussion;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        // Pick a discussion
        $discussion = Discussion::inRandomOrder()->first()
            ?? Discussion::factory()->create();

        // Ensure the user is enrolled in the course of that discussion
        $userId = Enrollment::where('course_id', $discussion->course_id)
            ->inRandomOrder()
            ->value('student_id');

        // Fallback: create a student and enroll them if none exist
        if (!$userId) {
            $student = \App\Models\User::factory()->create();

            Enrollment::create([
                'course_id' => $discussion->course_id,
                'student_id' => $student->id,
            ]);

            $userId = $student->id;
        }

        return [
            'discussion_id' => $discussion->id,
            'user_id' => $userId,
            'content' => fake()->sentence(8),
        ];
    }
}