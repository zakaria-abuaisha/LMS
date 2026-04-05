<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    public function definition(): array
    {
        // Generate percentages that sum to 100
        $assignment = fake()->numberBetween(10, 40);
        $quiz = fake()->numberBetween(10, 30);
        $mid = fake()->numberBetween(10, 30);
        $final = 100 - ($assignment + $quiz + $mid);

        return [
            'course_name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'course_code' => Str::random(9),
            'start_at' => $start = fake()->dateTimeBetween('-1 month', '+1 month'),
            'end_at' => fake()->dateTimeBetween($start, '+4 months'),
            'instructor_id' => User::query()->inRandomOrder()->value('id')
                            ?? User::factory(),
            'assignment_percent' => $assignment,
            'quiz_percent' => $quiz,
            'mid_percent' => $mid,
            'final_percent' => max($final, 0), // prevent negative
        ];
    }
}