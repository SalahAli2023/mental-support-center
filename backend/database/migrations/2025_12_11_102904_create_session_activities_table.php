<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('session_id')->index();
            $table->string('name_ar', 255); // الاسم بالعربية
            $table->string('name_en', 255); // الاسم بالإنجليزية
            $table->string('activity_type', 50); // text, audio, video, file, quiz
            $table->text('instructions_ar')->nullable(); // التعليمات بالعربية
            $table->text('instructions_en')->nullable(); // التعليمات بالإنجليزية
            $table->boolean('is_mandatory')->default(true);
            $table->timestamps();
            
            $table->foreign('session_id')->references('id')->on('program_sessions')->onDelete('cascade');
            $table->index(['session_id', 'activity_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_activities');
    }
};