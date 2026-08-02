<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_sessions', function (Blueprint $table) {
            $table->uuid('phase_id')->nullable()->after('program_id')->index();
            $table->foreign('phase_id')->references('id')->on('program_phases')->onDelete('cascade');
            $table->boolean('is_active')->default(true)->after('duration');
        });
    }

    public function down(): void
    {
        Schema::table('program_sessions', function (Blueprint $table) {
            $table->dropForeign(['phase_id']);
            $table->dropColumn(['phase_id', 'is_active']);
        });
    }
};


