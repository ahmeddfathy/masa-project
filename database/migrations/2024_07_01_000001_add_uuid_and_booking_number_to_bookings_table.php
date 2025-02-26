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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'uuid')) {
                $table->string('uuid')->nullable()->after('id');
            }

            if (!Schema::hasColumn('bookings', 'booking_number')) {
                $table->string('booking_number')->nullable()->after('uuid');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'uuid')) {
                $table->dropColumn('uuid');
            }

            if (Schema::hasColumn('bookings', 'booking_number')) {
                $table->dropColumn('booking_number');
            }
        });
    }
};
