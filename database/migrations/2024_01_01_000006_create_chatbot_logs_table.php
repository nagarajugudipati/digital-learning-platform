<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->text('response');
            $table->string('intent')->nullable()->comment('Detected intent/topic');
            $table->string('subject')->nullable()->comment('Detected subject');
            $table->decimal('confidence', 5, 4)->default(0)->comment('Match confidence 0-1');
            $table->boolean('was_helpful')->nullable()->comment('User feedback');
            $table->string('session_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_logs');
    }
};
