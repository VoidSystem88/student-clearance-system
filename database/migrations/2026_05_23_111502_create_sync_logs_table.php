<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyncLogsTable extends Migration
{
    public function up()
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending'); // pending, success, failed
            $table->integer('records_count')->default(0);
            $table->integer('total_lines')->nullable();
            $table->integer('errors')->default(0);
            $table->text('error_message')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('device')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('execution_time')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('sync_logs');
    }
}