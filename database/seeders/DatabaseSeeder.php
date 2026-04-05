<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Discussion;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1 Create instructors
        $instructors = User::factory()->count(3)->create();

        // 2 Create students
        $students = User::factory()->count(20)->create();

        // 3 Create courses for instructors
        $courses = collect();
        foreach ($instructors as $instructor) {
            $courses = $courses->merge(
                Course::factory()
                    ->count(2) // each instructor teaches 2 courses
                    ->state(['instructor_id' => $instructor->id])
                    ->create()
            );
        }

        // 4 Enroll students in courses
        foreach ($courses as $course) {
            $randomStudents = $students->random(10); // 10 students per course

            foreach ($randomStudents as $student) {
                Enrollment::factory()->forCourse($course->id)->forStudent($student->id)->create();
            }
        }
    
        // 5 Create discussions for each course
        foreach ($courses as $course) {
            // Instructor posts
            Discussion::factory()->count(2)->state([
                'course_id' => $course->id,
                'user_id' => $course->instructor_id
            ])->create();

            // Student posts
            $enrolledStudents = Enrollment::where('course_id', $course->id)->pluck('student_id');
            foreach ($enrolledStudents as $studentId) {
                Discussion::factory()->state([
                    'course_id' => $course->id,
                    'user_id' => $studentId,
                ])->create();
            }
        }

        // 6 Create comments for each discussion
        $discussions = Discussion::all();
        foreach ($discussions as $discussion) {
            Comment::factory()->count(3)->create([
                'discussion_id' => $discussion->id
            ]);
        }

    }
}