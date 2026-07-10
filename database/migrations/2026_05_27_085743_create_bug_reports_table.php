<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bug_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('student_id')->nullable();
            $table->string('type'); // bug, login_issue, registration_issue, other
            $table->text('message');
            $table->string('browser_info')->nullable();
            $table->string('url')->nullable();
            $table->string('status')->default('pending'); // pending, resolved, reviewing
            $table->text('admin_response')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bug_reports');
    }
};