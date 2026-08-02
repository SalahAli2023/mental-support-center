<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("UPDATE articles SET image = REPLACE(image, '\\\\', '/') WHERE image LIKE '%\\\\%'");
        DB::statement("UPDATE events SET media = REPLACE(media, '\\\\', '/') WHERE media LIKE '%\\\\%'");
        DB::statement("UPDATE users SET avatar = REPLACE(avatar, '\\\\', '/') WHERE avatar LIKE '%\\\\%'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed
    }
};

