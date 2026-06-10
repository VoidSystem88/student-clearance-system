<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('admin_2fa_enabled')->default(false);
            $table->string('admin_2fa_code')->nullable();
            $table->timestamp('admin_2fa_expires_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['admin_2fa_enabled', 'admin_2fa_code', 'admin_2fa_expires_at']);
        });
    }
};