<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('payment_status')->default('unpaid')->after('status');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('transaction_code')->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('transaction_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn([
                'payment_status',
                'payment_method',
                'transaction_code',
                'paid_at',
            ]);
        });
    }
};
