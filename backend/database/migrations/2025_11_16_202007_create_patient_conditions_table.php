<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        
        Schema::create('patient_conditions', function (Blueprint $table) {
            $table->id(); // استخدام bigint لتتوافق مع patients.id
            
            $table->unsignedBigInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            
            // الحالة الصحية
            $table->string('condition_ar', 255);
            $table->string('condition_en', 255);
            
            // معلومات التشخيص
            $table->date('diagnosis_date')->nullable();
            $table->enum('severity', ['mild', 'moderate', 'severe'])->default('mild');
            
            // العلاج والمتابعة
            $table->text('treatment_plan_ar')->nullable();
            $table->text('treatment_plan_en')->nullable();
            $table->text('medications_ar')->nullable();
            $table->text('medications_en')->nullable();
            
            // الحالة والنشاط
            $table->boolean('is_active')->default(true);
            $table->text('notes_ar')->nullable();
            $table->text('notes_en')->nullable();
            
            $table->timestamps();
            
            // الفهارس
            $table->index('patient_id');
            $table->index('is_active');
            $table->index('severity');
            $table->index('diagnosis_date');
            $table->index(['patient_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('patient_conditions');
    }
};