<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('birthdate')->nullable()->after('year_level');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('verification_otp')->nullable()->after('email_verified_at');
            $table->timestamp('otp_expires_at')->nullable()->after('verification_otp');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['birthdate', 'email_verified_at', 'verification_otp', 'otp_expires_at']);
        });
    }
};