<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sent_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('officer_id');
            $table->unsignedBigInteger('department_id');
            $table->string('report_title')->nullable();
            $table->string('event_name')->nullable();
            $table->text('notes')->nullable();
            $table->longText('csv_data');
            $table->string('attachment_path')->nullable();
            $table->integer('total_students')->default(0);
            $table->string('status')->default('sent');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('officer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sent_reports');
    }
};