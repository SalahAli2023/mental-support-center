<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('program_id')->constrained('programs')->onDelete('cascade');
            
            $table->timestamp('enrollment_date')->useCurrent();
            $table->integer('progress_percentage')->default(0);
            $table->string('status')->default('enrolled');
            
            $table->timestamps();
            
            // فهارس
            $table->index(['user_id', 'program_id']);
            $table->index('status');
            $table->unique(['user_id', 'program_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_programs');
    }
};