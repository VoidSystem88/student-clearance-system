<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department_year_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('year_level'); // 1st Year, 2nd Year, 3rd Year, 4th Year
            $table->timestamps();
            
            // Para iwas duplicate
            $table->unique(['department_id', 'year_level']);
            
            // Index para mas mabilis ang query
            $table->index('year_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_year_assignments');
    }
};