<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('cart_item_id')->nullable()->after('user_id')
                  ->constrained('cart_items')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['cart_item_id']);
            $table->dropColumn('cart_item_id');
        });
    }
};
