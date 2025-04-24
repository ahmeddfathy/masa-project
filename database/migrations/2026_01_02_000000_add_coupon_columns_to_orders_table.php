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
            $table->decimal('subtotal', 10, 2)->after('total_amount')->default(0);
            $table->decimal('discount_amount', 10, 2)->after('subtotal')->default(0);
            $table->foreignId('coupon_id')->nullable()->after('discount_amount')->constrained()->nullOnDelete();
            $table->string('coupon_code')->nullable()->after('coupon_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('coupon_code');
            $table->dropForeign(['coupon_id']);
            $table->dropColumn('coupon_id');
            $table->dropColumn('discount_amount');
            $table->dropColumn('subtotal');
        });
    }
};