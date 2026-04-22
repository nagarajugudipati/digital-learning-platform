<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_downloaded')->default(false);
            $table->integer('time_spent')->default(0)->comment('Seconds spent on lesson');
            $table->integer('views')->default(0);
            $table->timestamp('last_accessed')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'lesson_id']);
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_reports');
    }
};
