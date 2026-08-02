<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->integer('max_duration_days')->nullable()->after('duration');
            $table->integer('session_duration_minutes')->nullable()->after('max_duration_days');
            $table->integer('session_gap_days')->nullable()->after('session_duration_minutes');
            $table->integer('activity_gap_hours')->nullable()->after('session_gap_days');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn([
                'max_duration_days',
                'session_duration_minutes',
                'session_gap_days',
                'activity_gap_hours',
            ]);
        });
    }
};

