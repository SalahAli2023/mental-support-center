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
        // في قاعدة بيانات SQLite لا نحتاج هذا التعديل، و"change" غير مدعوم بشكل كامل
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('psychological_scales', function (Blueprint $table) {
            // تغيير نوع الحقل من string(500) إلى text لدعم base64 data URLs الكبيرة
            $table->text('image_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // تخطي عملية الرجوع أيضاً في حالة SQLite
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('psychological_scales', function (Blueprint $table) {
            // إعادة الحقل إلى string(500)
            $table->string('image_url', 500)->nullable()->change();
        });
    }
};





