<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('clearance_request_id')->unsigned();
            $table->bigInteger('student_id')->unsigned();
            $table->bigInteger('department_id')->unsigned();
            $table->enum('action', ['approved', 'rejected']);
            $table->text('remarks')->nullable();
            $table->string('staff_email');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_logs');
    }
};