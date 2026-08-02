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
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'psychological_scale_id')) {
                $table->foreignId('psychological_scale_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained('psychological_scales')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'psychological_scale_id')) {
                $table->dropForeign(['psychological_scale_id']);
                $table->dropColumn('psychological_scale_id');
            }
        });
    }
};




