<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id')->index();
            $table->uuid('scale_id')->index();
            $table->enum('assessment_type', ['pre', 'post']); // قبلي أو بعدي
            $table->boolean('is_mandatory')->default(true);
            $table->integer('order')->default(1); // ترتيب التقييم في البرنامج
            $table->text('instructions_ar')->nullable();
            $table->text('instructions_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('scale_id')->references('id')->on('psychological_scales')->onDelete('cascade');
            $table->index(['program_id', 'assessment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_assessments');
    }
};





