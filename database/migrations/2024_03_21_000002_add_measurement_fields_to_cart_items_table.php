<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->boolean('needs_appointment')->default(false);
            $table->string('color')->nullable();
            $table->string('size')->nullable();
        });
    }

    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['needs_appointment', 'color', 'size']);
        });
    }
};
