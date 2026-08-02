<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('therapists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('title_ar');
            $table->string('title_en');
            $table->json('methodologies_ar')->nullable();
            $table->json('methodologies_en')->nullable(); 
            $table->string('specialty_ar');
            $table->string('specialty_en');
            $table->integer('session_duration')->default(45);
            $table->integer('experience')->default(0);        
            $table->integer('total_sessions')->default(0);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->text('bio_ar')->nullable();
            $table->text('bio_en')->nullable();
            $table->enum('status', ['active', 'busy', 'away'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapists');
    }
};
