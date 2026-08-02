<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('session_activities', function (Blueprint $table) {
            // إضافة حقول جديدة للأنشطة
            $table->text('content_ar')->nullable()->after('instructions_en');
            $table->text('content_en')->nullable()->after('content_ar');
            $table->string('media_url')->nullable()->after('content_en'); // للفيديو والصوت
            $table->string('media_type')->nullable()->after('media_url'); // video, audio, image
            $table->integer('duration_minutes')->nullable()->after('media_type'); // مدة التمرين
            $table->json('activity_config')->nullable()->after('duration_minutes'); // إعدادات خاصة بالنشاط
            $table->integer('activity_order')->default(1)->after('activity_config');
            $table->boolean('is_active')->default(true)->after('activity_order');
            
            // تحديث activity_type لدعم أنواع أكثر
            // text, video, audio, form, exercise, reflection_questions
            $table->index(['session_id', 'activity_order']);
        });
    }

    public function down(): void
    {
        Schema::table('session_activities', function (Blueprint $table) {
            $table->dropColumn([
                'content_ar',
                'content_en',
                'media_url',
                'media_type',
                'duration_minutes',
                'activity_config',
                'activity_order',
                'is_active'
            ]);
        });
    }
};





