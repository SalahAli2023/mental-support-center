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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('slug')->unique();
            $table->enum('type', ['أمسيات', 'ورش عمل', 'فعاليات', 'evenings', 'workshops', 'events']);
            $table->text('description_ar');
            $table->text('description_en');
            $table->longText('full_description_ar')->nullable();
            $table->longText('full_description_en')->nullable();
            $table->string('media')->nullable();
            $table->enum('media_type', ['image', 'video'])->default('image');
            $table->date('date');
            $table->string('duration')->nullable();
            $table->string('location_ar')->nullable();
            $table->string('location_en')->nullable();
            $table->json('topics_ar')->nullable(); // Array of topics in Arabic
            $table->json('topics_en')->nullable(); // Array of topics in English
            $table->json('speakers')->nullable(); // Array of speaker objects
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
