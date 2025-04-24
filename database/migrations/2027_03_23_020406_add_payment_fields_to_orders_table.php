<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_transaction_id')->nullable();
            $table->string('payment_id')->nullable()->after('payment_transaction_id');
            $table->decimal('amount_paid', 10, 2)->default(0)->after('payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_transaction_id', 'payment_id', 'amount_paid']);
        });
    }
};
