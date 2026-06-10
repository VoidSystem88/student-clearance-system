<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Check if column exists before adding
        if (!Schema::hasColumn('users', 'course_year')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('course_year')->nullable()->after('year_level');
            });
        }

        // Update existing records: combine course and year_level
        DB::statement("UPDATE users SET course_year = CONCAT(course, ' - ', year_level) WHERE course_year IS NULL AND course IS NOT NULL AND year_level IS NOT NULL");
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'course_year')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('course_year');
            });
        }
    }
};