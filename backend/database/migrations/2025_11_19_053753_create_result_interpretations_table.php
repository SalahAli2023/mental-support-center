<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('result_interpretations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('scale_id', 36);
            $table->integer('min_score');
            $table->integer('max_score');
            $table->string('interpretation_label_ar', 100);
            $table->string('interpretation_label_en', 100);
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('color', 20)->nullable();
            $table->timestamps();
            
            $table->foreign('scale_id')
                  ->references('id')
                  ->on('psychological_scales')
                  ->onDelete('cascade');
                  
            $table->index(['scale_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('result_interpretations');
    }
};