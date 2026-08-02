<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('activity_id')->constrained('session_activities')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('status')->default('pending');
            $table->timestamp('submission_date')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // فهارس
            $table->index(['activity_id', 'user_id']);
            $table->index('status');
            $table->unique(['activity_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_submissions');
    }
};