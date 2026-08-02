<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('therapist_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')
                ->constrained('video_sessions')
                ->cascadeOnDelete();
            $table->foreignId('therapist_id')
                ->constrained('therapists')
                ->cascadeOnDelete();
            $table->foreignId('client_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('patient_id')
                ->nullable()
                ->constrained('patients')
                ->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique('session_id');
            $table->index(['therapist_id', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapist_reviews');
    }
};

