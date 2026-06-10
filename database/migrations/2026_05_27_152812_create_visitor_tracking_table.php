<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('visitor_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->nullable();
            $table->string('device_type')->nullable(); // mobile, tablet, desktop
            $table->string('device_model')->nullable(); // iPhone 14, Samsung S23, etc.
            $table->string('os')->nullable(); // iOS, Android, Windows, macOS
            $table->string('browser')->nullable(); // Chrome, Safari, Firefox
            $table->string('network_type')->nullable(); // wifi, cellular, ethernet
            $table->string('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->string('page_visited')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitor_tracking');
    }
};