<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'session_gap_hours')) {
                $table->integer('session_gap_hours')->nullable()->after('session_duration_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            if (Schema::hasColumn('programs', 'session_gap_hours')) {
                $table->dropColumn('session_gap_hours');
            }
        });
    }
};

