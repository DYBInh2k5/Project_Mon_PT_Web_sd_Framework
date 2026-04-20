<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('role');
        });

        DB::table('users')
            ->where('role', 'viewer')
            ->update(['role' => 'user']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'user')
            ->update(['role' => 'viewer']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
