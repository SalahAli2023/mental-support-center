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
        Schema::create('legal_resource_categories', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->timestamps();
        });

        Schema::create('legal_resources', function (Blueprint $table) {
            $table->id();
            $table->string('law_type'); // e.g., 'constitution', 'womenRights', 'childrenRights'
            $table->string('article_number_ar')->nullable();
            $table->string('article_number_en')->nullable();
            $table->text('text_ar');
            $table->text('text_en');
            $table->foreignId('category_id')->nullable()->constrained('legal_resource_categories')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_resources');
        Schema::dropIfExists('legal_resource_categories');
    }
};
