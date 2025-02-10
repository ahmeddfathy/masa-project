<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_addons', function (Blueprint $table) {
            $table->foreignId('package_id')
                  ->after('id')
                  ->constrained('packages')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('package_addons', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn('package_id');
        });
    }
};
