<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group'); // identity, about, vision, achievements, contact
            $table->string('key');
            $table->text('value')->nullable();
            $table->text('value_ar')->nullable();
            $table->text('value_en')->nullable();
            $table->string('type')->default('text'); // text, image, file, json, array
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['group', 'key']);
            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
