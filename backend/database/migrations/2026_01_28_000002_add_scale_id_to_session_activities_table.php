<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('session_activities', 'scale_id')) {
            Schema::table('session_activities', function (Blueprint $table) {
                $table->uuid('scale_id')->nullable()->after('media_type');
                $table->foreign('scale_id')->references('id')->on('psychological_scales')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('session_activities', 'scale_id')) {
            Schema::table('session_activities', function (Blueprint $table) {
                $table->dropForeign(['scale_id']);
                $table->dropColumn('scale_id');
            });
        }
    }
};

