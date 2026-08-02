<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scale_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('scale_id', 36);
            $table->text('question_text_ar');
            $table->text('question_text_en');
            $table->integer('question_order')->default(0);
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
        Schema::dropIfExists('scale_questions');
    }
};