<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name_ar', 255); // الاسم بالعربية
            $table->string('name_en', 255); // الاسم بالإنجليزية
            $table->text('description_ar')->nullable(); // الوصف بالعربية
            $table->text('description_en')->nullable(); // الوصف بالإنجليزية
            $table->string('target_category_ar', 255)->nullable(); // الفئة بالعربية
            $table->string('target_category_en', 255)->nullable(); // الفئة بالإنجليزية
            $table->integer('duration')->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            $table->uuid('scale_id')->nullable()->index();
            $table->string('image_url', 500)->nullable();
            $table->timestamps();

            $table->foreign('scale_id')->references('id')->on('psychological_scales')->onDelete('set null');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};