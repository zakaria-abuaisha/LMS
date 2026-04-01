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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->smallInteger("grade")->nullable();
            $table->timestamp("submitted_at");
            $table->foreignId('assignment_id')
                ->constrained(table: 'assignments', indexName: 'id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('student_id')
                ->constrained(table: 'users', indexName: 'id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
