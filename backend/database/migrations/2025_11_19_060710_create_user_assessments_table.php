<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->char('scale_id', 36);
            $table->integer('total_score');
            $table->string('interpretation_level');
            $table->json('assessment_data')->nullable();
            $table->timestamp('completed_at')->useCurrent();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'scale_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_assessments');
    }
};