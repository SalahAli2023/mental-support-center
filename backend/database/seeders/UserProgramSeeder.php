<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\User;
use App\Models\UserProgram;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على المستخدمين والبرامج
        $users = User::where('role', '!=', 'Admin')->take(10)->get();
        $programs = Program::where('status', 'active')->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  يجب إنشاء مستخدمين أولاً!');
            return;
        }

        if ($programs->isEmpty()) {
            $this->command->warn('⚠️  يجب إنشاء برامج نشطة أولاً! قم بتشغيل ProgramSeeder');
            return;
        }

        $enrollmentsCreated = 0;

        // لكل مستخدم، نسجله في 1-3 برامج عشوائية
        foreach ($users as $user) {
            $selectedPrograms = $programs->random(min(3, $programs->count()));
            
            foreach ($selectedPrograms as $program) {
                // التحقق من عدم التسجيل مسبقاً
                $existing = UserProgram::where('user_id', $user->id)
                                      ->where('program_id', $program->id)
                                      ->exists();
                
                if ($existing) {
                    continue;
                }

                $status = $this->getRandomStatus();
                $progress = $this->getProgressForStatus($status);
                
                UserProgram::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'program_id' => $program->id,
                    'enrollment_date' => now()->subDays(rand(1, 60)),
                    'progress_percentage' => $progress,
                    'status' => $status,
                    'started_at' => $status !== 'enrolled' ? now()->subDays(rand(1, 50)) : null,
                    'completed_at' => $status === 'completed' ? now()->subDays(rand(1, 10)) : null,
                ]);

                $enrollmentsCreated++;
            }

            $this->command->info("   ✓ تم تسجيل المستخدم: {$user->name} في البرامج");
        }

        $this->command->info("\n✅ تم إنشاء {$enrollmentsCreated} تسجيل بنجاح!");
        
        $this->command->info("📊 إحصائيات التسجيلات:");
        $this->command->info("   - مسجل: " . UserProgram::where('status', 'enrolled')->count());
        $this->command->info("   - قيد التنفيذ: " . UserProgram::where('status', 'in_progress')->count());
        $this->command->info("   - مكتمل: " . UserProgram::where('status', 'completed')->count());
        $this->command->info("   - متوقف: " . UserProgram::where('status', 'dropped')->count());
    }

    private function getRandomStatus()
    {
        $statuses = ['enrolled', 'in_progress', 'completed', 'dropped'];
        $weights = [20, 50, 25, 5]; // احتمالات
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return 'enrolled';
    }

    private function getProgressForStatus($status)
    {
        return match($status) {
            'enrolled' => 0,
            'in_progress' => rand(10, 90),
            'completed' => 100,
            'dropped' => rand(5, 50),
            default => 0
        };
    }
}




