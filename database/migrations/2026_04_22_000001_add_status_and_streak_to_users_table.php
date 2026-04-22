<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->nullable()->after('is_active'); // pending, approved, rejected (used for teachers)
            $table->unsignedInteger('streak_count')->default(0)->after('status');
            $table->timestamp('last_login_at')->nullable()->after('streak_count');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'streak_count', 'last_login_at']);
        });
    }
};
