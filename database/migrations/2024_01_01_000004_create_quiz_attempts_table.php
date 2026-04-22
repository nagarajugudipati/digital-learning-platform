<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->integer('score')->default(0);
            $table->integer('total_marks')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->json('answers')->nullable()->comment('JSON of question_id => selected_answer');
            $table->enum('status', ['started', 'completed', 'timed_out'])->default('started');
            $table->boolean('passed')->default(false);
            $table->integer('time_taken')->nullable()->comment('Seconds taken');
            $table->integer('attempt_number')->default(1);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'quiz_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
