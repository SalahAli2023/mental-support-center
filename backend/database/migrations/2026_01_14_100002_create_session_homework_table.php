<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_homework', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('session_id')->index();
            $table->string('title_ar', 255);
            $table->string('title_en', 255);
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->text('instructions_ar')->nullable();
            $table->text('instructions_en')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->enum('completion_type', ['confirmation', 'text_input', 'file_upload', 'form'])->default('confirmation');
            $table->json('completion_config')->nullable(); // إعدادات خاصة بنوع الإكمال
            $table->integer('homework_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('session_id')->references('id')->on('program_sessions')->onDelete('cascade');
            $table->index(['session_id', 'homework_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_homework');
    }
};





