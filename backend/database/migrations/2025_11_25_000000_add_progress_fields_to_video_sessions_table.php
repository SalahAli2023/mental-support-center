<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('video_sessions')) {
            return;
        }

        Schema::table('video_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('video_sessions', 'progress_score')) {
                $table->unsignedTinyInteger('progress_score')
                    ->default(0)
                    ->after('notes');
            }

            if (!Schema::hasColumn('video_sessions', 'therapist_notes')) {
                $table->text('therapist_notes')
                    ->nullable()
                    ->after('progress_score');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('video_sessions')) {
            return;
        }

        Schema::table('video_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('video_sessions', 'therapist_notes')) {
                $table->dropColumn('therapist_notes');
            }

            if (Schema::hasColumn('video_sessions', 'progress_score')) {
                $table->dropColumn('progress_score');
            }
        });
    }
};


