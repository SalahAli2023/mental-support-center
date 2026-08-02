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
        Schema::table('user_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('user_messages', 'response')) {
                $table->text('response')->nullable()->after('message');
            }

            if (!Schema::hasColumn('user_messages', 'responded_by')) {
                $table->foreignId('responded_by')
                    ->nullable()
                    ->after('response')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('user_messages', 'responded_at')) {
                $table->timestamp('responded_at')->nullable()->after('responded_by');
            }

            if (!Schema::hasColumn('user_messages', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('responded_at');
            }

            if (!Schema::hasColumn('user_messages', 'public_at')) {
                $table->timestamp('public_at')->nullable()->after('is_public');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_messages', function (Blueprint $table) {
            if (Schema::hasColumn('user_messages', 'public_at')) {
                $table->dropColumn('public_at');
            }
            if (Schema::hasColumn('user_messages', 'is_public')) {
                $table->dropColumn('is_public');
            }
            if (Schema::hasColumn('user_messages', 'responded_at')) {
                $table->dropColumn('responded_at');
            }
            if (Schema::hasColumn('user_messages', 'responded_by')) {
                $table->dropForeign(['responded_by']);
                $table->dropColumn('responded_by');
            }
            if (Schema::hasColumn('user_messages', 'response')) {
                $table->dropColumn('response');
            }
        });
    }
};




