<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('enable_custom_color')->default(false);
            $table->boolean('enable_custom_size')->default(false);
            $table->boolean('enable_color_selection')->default(false);
            $table->boolean('enable_size_selection')->default(false);
            $table->boolean('enable_appointments')->default(true);
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'enable_custom_color',
                'enable_custom_size',
                'enable_color_selection',
                'enable_size_selection',
                'enable_appointments'
            ]);
        });
    }
};
