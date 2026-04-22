<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 8, 2)->default(0);
            $table->string('thumbnail')->nullable();
            $table->string('subject')->nullable();
            $table->string('class_level')->nullable();
            $table->enum('status', ['draft', 'pending', 'published', 'rejected'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
