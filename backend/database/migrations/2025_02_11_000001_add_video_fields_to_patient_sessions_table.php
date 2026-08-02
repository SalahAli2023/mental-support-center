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
        // إذا لم تكن جدول patient_sessions موجوداً (خاصة في SQLite أثناء التطوير)، نتخطى هذا المايغريشن
        if (!Schema::hasTable('patient_sessions')) {
            return;
        }

        Schema::table('patient_sessions', function (Blueprint $table) {
            $table->string('video_provider')->nullable()->after('location');
            $table->string('video_meeting_id')->nullable()->after('video_provider');
            $table->string('video_password')->nullable()->after('video_meeting_id');
            $table->string('video_join_url')->nullable()->after('video_password');
            $table->string('video_start_url')->nullable()->after('video_join_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('patient_sessions')) {
            return;
        }

        Schema::table('patient_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'video_provider',
                'video_meeting_id',
                'video_password',
                'video_join_url',
                'video_start_url',
            ]);
        });
    }
};

