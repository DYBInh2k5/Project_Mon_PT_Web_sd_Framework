<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_tag', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('article_id');
            $table->integer('tag_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_tag');
    }
};
