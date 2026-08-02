<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('question_id', 36);
            $table->string('option_text_ar', 500);
            $table->string('option_text_en', 500);
            $table->integer('score_value');
            $table->integer('option_order')->default(0);
            $table->timestamps();
            
            $table->foreign('question_id')
                  ->references('id')
                  ->on('scale_questions')
                  ->onDelete('cascade');
                  
            $table->index(['question_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_options');
    }
};