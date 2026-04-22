<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->enum('payment_status', ['free', 'pending', 'paid'])->default('free')->after('enrolled_at');
            $table->decimal('amount_paid', 8, 2)->default(0)->after('payment_status');
            $table->string('transaction_id')->nullable()->after('amount_paid');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'amount_paid', 'transaction_id']);
        });
    }
};
