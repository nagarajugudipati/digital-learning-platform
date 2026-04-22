<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->enum('type', ['video', 'pdf', 'image', 'text']);
            $table->string('file_path')->nullable();
            $table->text('content_text')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['lesson_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_contents');
    }
};
