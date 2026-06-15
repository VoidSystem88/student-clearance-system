<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('verified_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('filename');
            $table->text('csv_data'); // store csv content as text
            $table->string('event_name')->nullable(); // kung may specific event
            $table->integer('total_records')->default(0);
            $table->date('export_date');
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->timestamp('expires_at')->nullable(); // optional: auto-delete after x days
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('verified_exports');
    }
};