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
        Schema::create('therapist_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapist_id')->constrained()->onDelete('cascade');
            
            // اليوم
            $table->enum('day', [
                'saturday', 
                'sunday', 
                'monday', 
                'tuesday', 
                'wednesday', 
                'thursday', 
                'friday'
            ]);
            
            // الفترات الزمنية
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('available')->default(true);
            
            // تكرار الموعد
            $table->enum('recurrence', ['weekly', 'biweekly', 'monthly'])->default('weekly');
            $table->integer('slot_duration')->default(60); // مدة كل حجز بالدقائق
            
            $table->timestamps();
            
            // الفهارس
            $table->index(['therapist_id', 'day', 'available']);
            $table->unique(['therapist_id', 'day', 'start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_herapist_schedules');
    }
};
