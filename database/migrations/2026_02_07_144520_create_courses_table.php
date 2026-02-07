<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string("course_name");
            $table->text("description");
            $table->uuid("course_code")->unique();
            $table->dateTime("start_at");
            $table->dateTime("end_at");
            $table->foreignId('instructure_id')
                ->constrained(table: 'users', indexName: 'id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->smallInteger("assignment_percent");
            $table->smallInteger("quiz_percent");
            $table->smallInteger("mid_percent");
            $table->smallInteger("final_percent");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
