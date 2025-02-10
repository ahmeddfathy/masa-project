<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained();
            $table->foreignId('package_id')->constrained();
            $table->date('booking_date');
            $table->date('session_date');
            $table->time('session_time');
            $table->string('baby_name', 100)->nullable();
            $table->date('baby_birth_date')->nullable();
            $table->enum('gender', ['ذكر', 'أنثى'])->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
