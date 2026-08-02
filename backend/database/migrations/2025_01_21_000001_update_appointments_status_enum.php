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
            $hasData = DB::table('appointments')->count() > 0;

            Schema::rename('appointments', 'appointments_old');

            // إنشاء الجدول مع CHECK constraint مباشرة
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

            if ($hasData) {
                $appointments = DB::table('appointments_old')->get();
                foreach ($appointments as $appointment) {
                    DB::table('appointments')->insert([
                        'id' => $appointment->id,
                        'client_id' => $appointment->client_id,
                        'therapist_id' => $appointment->therapist_id,
                        'starts_at' => $appointment->starts_at,
                        'ends_at' => $appointment->ends_at,
                        'status' => $appointment->status === 'scheduled' ? 'pending' : $appointment->status,
                        'notes' => $appointment->notes,
                        'cancellation_reason' => $appointment->cancellation_reason,
                        'created_at' => $appointment->created_at,
                        'updated_at' => $appointment->updated_at,
                    ]);
                }
            }

            Schema::drop('appointments_old');
        } else {
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('appointments')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            $hasData = DB::table('appointments')->count() > 0;

            Schema::rename('appointments', 'appointments_new');

            Schema::create('appointments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('therapist_id')->constrained('therapists')->onDelete('cascade');
                $table->dateTime('starts_at');
                $table->dateTime('ends_at')->nullable();
                $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
                $table->text('notes')->nullable();
                $table->text('cancellation_reason')->nullable();
                $table->timestamps();
            });

            if ($hasData) {
                $appointments = DB::table('appointments_new')->get();
                foreach ($appointments as $appointment) {
                    DB::table('appointments')->insert([
                        'id' => $appointment->id,
                        'client_id' => $appointment->client_id,
                        'therapist_id' => $appointment->therapist_id,
                        'starts_at' => $appointment->starts_at,
                        'ends_at' => $appointment->ends_at,
                        'status' => $appointment->status === 'pending' ? 'scheduled' : $appointment->status,
                        'notes' => $appointment->notes,
                        'cancellation_reason' => $appointment->cancellation_reason,
                        'created_at' => $appointment->created_at,
                        'updated_at' => $appointment->updated_at,
                    ]);
                }
            }

            Schema::drop('appointments_new');
        } else {
            DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled'");
        }
    }
};

