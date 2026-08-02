<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id')->index();
            $table->string('title_ar', 255); // العنوان بالعربية
            $table->string('title_en', 255); // العنوان بالإنجليزية
            $table->integer('session_order')->default(1);
            $table->text('goal_ar')->nullable(); // الهدف بالعربية
            $table->text('goal_en')->nullable(); // الهدف بالإنجليزية
            $table->integer('duration')->nullable();
            $table->timestamps();
            
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->index(['program_id', 'session_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_sessions');
    }
};