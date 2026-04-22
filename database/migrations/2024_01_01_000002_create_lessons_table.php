<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('subject');
            $table->string('class_level')->comment('e.g., Class 6, Class 7');
            $table->string('file_path')->nullable();
            $table->enum('file_type', ['pdf', 'video', 'text', 'image'])->default('pdf');
            $table->text('content')->nullable()->comment('Text content for online reading');
            $table->string('thumbnail')->nullable();
            $table->integer('duration_minutes')->nullable()->comment('For video lessons');
            $table->enum('status', ['pending', 'approved', 'rejected', 'published'])->default('pending');
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['subject', 'class_level', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
