<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('program_sessions', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('duration');
            }
        });
    }

    public function down(): void
    {
        Schema::table('program_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('program_sessions', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};




