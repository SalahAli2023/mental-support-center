<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_programs', function (Blueprint $table) {
            $table->uuid('current_phase_id')->nullable()->after('status')->index();
            $table->uuid('current_session_id')->nullable()->after('current_phase_id')->index();
            $table->uuid('current_activity_id')->nullable()->after('current_session_id')->index();
            $table->timestamp('started_at')->nullable()->after('enrollment_date');
            $table->timestamp('completed_at')->nullable()->after('progress_percentage');
            $table->json('progress_data')->nullable()->after('completed_at'); // بيانات التقدم التفصيلية
            
            $table->foreign('current_phase_id')->references('id')->on('program_phases')->onDelete('set null');
            $table->foreign('current_session_id')->references('id')->on('program_sessions')->onDelete('set null');
            $table->foreign('current_activity_id')->references('id')->on('session_activities')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('user_programs', function (Blueprint $table) {
            $table->dropForeign(['current_phase_id']);
            $table->dropForeign(['current_session_id']);
            $table->dropForeign(['current_activity_id']);
            $table->dropColumn([
                'current_phase_id',
                'current_session_id',
                'current_activity_id',
                'started_at',
                'completed_at',
                'progress_data'
            ]);
        });
    }
};





