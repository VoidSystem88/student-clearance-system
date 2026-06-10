<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clearance_requests', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('clearance_requests', 'request_message')) {
                $table->text('request_message')->nullable()->after('attachment_path');
            }
            
            if (!Schema::hasColumn('clearance_requests', 'is_manually_verified')) {
                $table->boolean('is_manually_verified')->default(false)->after('status');
            }
            
            if (!Schema::hasColumn('clearance_requests', 'verified_list_match')) {
                $table->boolean('verified_list_match')->default(false)->after('is_manually_verified');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clearance_requests', function (Blueprint $table) {
            $table->dropColumn([
                'request_message',
                'is_manually_verified',
                'verified_list_match',
            ]);
        });
    }
};