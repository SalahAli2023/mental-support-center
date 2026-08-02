<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('psychological_scales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('category_id', 36)->nullable();
            $table->string('name_ar', 255);
            $table->string('name_en', 255);
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('image_url', 500)->nullable();
            $table->integer('max_score')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');
                  
            $table->index(['category_id']);
            $table->index(['is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('psychological_scales');
    }
};