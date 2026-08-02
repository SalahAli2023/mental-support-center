<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_assessment_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_assessment_id')->index();
            $table->uuid('user_id')->index();
            $table->uuid('user_assessment_id')->nullable()->index(); // ربط بنتيجة المقياس
            $table->integer('total_score')->nullable();
            $table->string('interpretation_level')->nullable();
            $table->json('assessment_data')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('program_assessment_id')->references('id')->on('program_assessments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_assessment_id')->references('id')->on('user_assessments')->onDelete('set null');
            $table->index(['program_assessment_id', 'user_id']);
            $table->unique(['program_assessment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_assessment_results');
    }
};





