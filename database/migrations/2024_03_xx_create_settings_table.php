<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('text');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // إضافة الإعدادات الافتراضية
        DB::table('settings')->insert([
            [
                'key' => 'max_concurrent_bookings',
                'value' => '1',
                'type' => 'number',
                'description' => 'الحد الأقصى للحجوزات المتزامنة'
            ],
            [
                'key' => 'show_store_appointments',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'إظهار قائمة حجز مواعيد المتجر للمستخدمين'
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
