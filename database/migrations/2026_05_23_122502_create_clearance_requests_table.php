<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clearance_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id')->unsigned();
            $table->bigInteger('department_id')->unsigned();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('attachment_path')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'department_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clearance_requests');
    }
};