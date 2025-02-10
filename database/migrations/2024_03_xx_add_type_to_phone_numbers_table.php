<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phone_numbers', function (Blueprint $table) {
            $table->enum('type', ['mobile', 'home', 'work', 'other'])->default('mobile')->after('phone');
            $table->boolean('is_primary')->default(false)->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('phone_numbers', function (Blueprint $table) {
            $table->dropColumn(['type', 'is_primary']);
        });
    }
};
