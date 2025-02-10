<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery', function (Blueprint $table) {
            $table->id();
            $table->string('image_url');
            $table->string('caption');
            $table->string('category', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery');
    }
};
