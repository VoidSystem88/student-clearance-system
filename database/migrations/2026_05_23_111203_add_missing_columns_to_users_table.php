<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Idagdag lang kung wala pa ang column na 'birthdate'
            if (!Schema::hasColumn('users', 'birthdate')) {
                $table->date('birthdate')->nullable()->after('last_name');
            }
            
            // Idagdag lang kung wala pa ang column na 'year_level'
            if (!Schema::hasColumn('users', 'year_level')) {
                $table->string('year_level')->nullable()->after('course');
            }
            
            // Idagdag lang kung wala pa ang column na 'course_year'
            if (!Schema::hasColumn('users', 'course_year')) {
                $table->string('course_year')->nullable()->after('year_level');
            }
            
            // Idagdag lang kung wala pa ang column na 'is_cleared'
            if (!Schema::hasColumn('users', 'is_cleared')) {
                $table->boolean('is_cleared')->default(false)->after('is_active');
            }
            
            // Idagdag lang kung wala pa ang column na 'cleared_at'
            if (!Schema::hasColumn('users', 'cleared_at')) {
                $table->timestamp('cleared_at')->nullable()->after('is_cleared');
            }
            
            // Idagdag lang kung wala pa ang column na 'admin_2fa_enabled'
            if (!Schema::hasColumn('users', 'admin_2fa_enabled')) {
                $table->boolean('admin_2fa_enabled')->default(false)->after('cleared_at');
            }
            
            // Idagdag lang kung wala pa ang column na 'admin_2fa_code'
            if (!Schema::hasColumn('users', 'admin_2fa_code')) {
                $table->string('admin_2fa_code', 10)->nullable()->after('admin_2fa_enabled');
            }
            
            // Idagdag lang kung wala pa ang column na 'admin_2fa_expires_at'
            if (!Schema::hasColumn('users', 'admin_2fa_expires_at')) {
                $table->timestamp('admin_2fa_expires_at')->nullable()->after('admin_2fa_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'birthdate',
                'year_level',
                'course_year',
                'is_cleared',
                'cleared_at',
                'admin_2fa_enabled',
                'admin_2fa_code',
                'admin_2fa_expires_at'
            ]);
        });
    }
};