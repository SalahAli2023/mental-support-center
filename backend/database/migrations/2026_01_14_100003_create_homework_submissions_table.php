<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homework_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('homework_id')->index();
            $table->uuid('user_id')->index();
            $table->enum('status', ['pending', 'submitted', 'completed', 'approved'])->default('pending');
            $table->text('submission_text')->nullable();
            $table->json('submission_data')->nullable(); // للبيانات المعقدة (نماذج، إلخ)
            $table->string('file_url')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            $table->foreign('homework_id')->references('id')->on('session_homework')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['homework_id', 'user_id']);
            $table->index('status');
            $table->unique(['homework_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homework_submissions');
    }
};





