<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('type', ['mcq', 'true_false', 'text'])->default('mcq')->after('question_text');
            // Make option columns nullable to support true_false and text types
            $table->string('option_a')->nullable()->change();
            $table->string('option_b')->nullable()->change();
            $table->string('option_c')->nullable()->change();
            $table->string('option_d')->nullable()->change();
            $table->string('correct_answer')->change(); // allow longer values for text answers
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
