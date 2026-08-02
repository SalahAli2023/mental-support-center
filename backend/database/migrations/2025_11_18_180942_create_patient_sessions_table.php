<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('therapist_id')->constrained('users')->onDelete('cascade');
            $table->string('title_ar');
            $table->string('title_en');
            $table->date('session_date');
            $table->time('session_time');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->integer('progress')->default(0);
            $table->enum('type', ['individual', 'group', 'family', 'assessment', 'followup'])->default('individual');
            $table->enum('location', ['clinic', 'online', 'home'])->default('clinic');
            $table->text('notes_ar')->nullable();
            $table->text('notes_en')->nullable();
            $table->text('report_ar')->nullable();
            $table->text('report_en')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_sessions');
    }
};