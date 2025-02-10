<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the existing status column
            $table->dropColumn('status');

            // Add new columns
            $table->string('payment_method')->after('phone'); // cash or card
            $table->string('payment_status')->default('pending')->after('payment_method');
            $table->string('order_status')->default('pending')->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'order_status']);
            $table->string('status')->default('pending'); // Restore the original status column
        });
    }
};
