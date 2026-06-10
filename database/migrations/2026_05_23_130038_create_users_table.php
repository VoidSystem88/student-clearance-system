<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'staff', 'student', 'support', 'super_admin'])->default('student');
            $table->string('student_id')->nullable();
            $table->string('account_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birthdate')->nullable();                    // <-- IDAGDAG
            $table->string('course')->nullable();
            $table->string('year_level')->nullable();                 // <-- IDAGDAG
            $table->string('course_year')->nullable();                // <-- IDAGDAG
            $table->string('department_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_cleared')->default(false);            // <-- IDAGDAG (kung meron)
            $table->timestamp('cleared_at')->nullable();              // <-- IDAGDAG (kung meron)
            $table->boolean('admin_2fa_enabled')->default(false);     // <-- IDAGDAG (kung meron)
            $table->string('admin_2fa_code', 10)->nullable();         // <-- IDAGDAG (kung meron)
            $table->timestamp('admin_2fa_expires_at')->nullable();    // <-- IDAGDAG (kung meron)
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};