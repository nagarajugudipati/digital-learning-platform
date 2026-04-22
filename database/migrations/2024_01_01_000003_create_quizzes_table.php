<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('subject');
            $table->string('class_level');
            $table->integer('time_limit')->default(30)->comment('In minutes');
            $table->integer('passing_marks')->default(40)->comment('Percentage');
            $table->integer('total_marks')->default(0);
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->integer('max_attempts')->default(3);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->text('question_text');
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->enum('correct_answer', ['a', 'b', 'c', 'd']);
            $table->text('explanation')->nullable();
            $table->integer('marks')->default(1);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
    }
};
