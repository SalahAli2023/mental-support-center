<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('appointments')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            // التحقق من وجود CHECK constraint قديم
            $tableInfo = DB::select("PRAGMA table_info(appointments)");
            $hasOldConstraint = false;
            
            // التحقق من وجود constraint قديم
            $sql = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='appointments'");
            if (!empty($sql) && isset($sql[0]->sql)) {
                $createSql = $sql[0]->sql;
                // التحقق من وجود constraint قديم (scheduled, completed, cancelled فقط)
                if (strpos($createSql, "CHECK (status IN ('scheduled', 'completed', 'cancelled')") !== false) {
                    $hasOldConstraint = true;
                }
            }

            if ($hasOldConstraint) {
                $hasData = DB::table('appointments')->count() > 0;
                $oldData = [];

                if ($hasData) {
                    $oldData = DB::table('appointments')->get()->toArray();
                }

                Schema::dropIfExists('appointments');

                // إنشاء الجدول مع CHECK constraint جديد
                DB::statement("
                    CREATE TABLE appointments (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        client_id INTEGER NOT NULL,
                        therapist_id INTEGER NOT NULL,
                        starts_at DATETIME NOT NULL,
                        ends_at DATETIME NULL,
                        status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'confirmed', 'completed', 'cancelled')),
                        notes TEXT NULL,
                        cancellation_reason TEXT NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
                        FOREIGN KEY (therapist_id) REFERENCES therapists(id) ON DELETE CASCADE
                    )
                ");

                // إعادة إدخال البيانات مع تحويل 'scheduled' إلى 'pending'
                if ($hasData && !empty($oldData)) {
                    foreach ($oldData as $row) {
                        $status = $row->status === 'scheduled' ? 'pending' : $row->status;
                        DB::table('appointments')->insert([
                            'id' => $row->id,
                            'client_id' => $row->client_id,
                            'therapist_id' => $row->therapist_id,
                            'starts_at' => $row->starts_at,
                            'ends_at' => $row->ends_at,
                            'status' => $status,
                            'notes' => $row->notes,
                            'cancellation_reason' => $row->cancellation_reason ?? null,
                            'created_at' => $row->created_at,
                            'updated_at' => $row->updated_at,
                        ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // لا حاجة للتراجع - هذا migration للإصلاح فقط
    }
};
