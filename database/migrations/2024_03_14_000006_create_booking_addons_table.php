<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_addons', function (Blueprint $table) {
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('addon_id')->constrained('package_addons')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('price_at_booking', 10, 2);
            $table->primary(['booking_id', 'addon_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_addons');
    }
};
