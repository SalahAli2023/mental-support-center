<?php

namespace Database\Seeders;

use App\Models\ActivityProgress;
use App\Models\SessionActivity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ActivityProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على المستخدمين والأنشطة
        $users = User::where('role', '!=', 'Admin')->take(5)->get();
        $activities = SessionActivity::all();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  يجب إنشاء مستخدمين أولاً!');
            return;
        }

        if ($activities->isEmpty()) {
            $this->command->warn('⚠️  يجب إنشاء أنشطة أولاً! قم بتشغيل SessionActivitySeeder');
            return;
        }

        $progressCreated = 0;

        // لكل مستخدم، ننشئ تقدم في بعض الأنشطة
        foreach ($users as $user) {
            // نختار 10-20 نشاط عشوائي
            $selectedActivities = $activities->random(min(20, $activities->count()));
            
            foreach ($selectedActivities as $activity) {
                $status = $this->getRandomStatus();
                
                ActivityProgress::create([
                    'id' => (string) Str::uuid(),
                    'activity_id' => $activity->id,
                    'user_id' => $user->id,
                    'status' => $status,
                    'progress_percentage' => $this->getProgressPercentage($status),
                    'started_at' => $status !== 'not_started' ? now()->subDays(rand(1, 30)) : null,
                    'completed_at' => $status === 'completed' ? now()->subDays(rand(1, 10)) : null,
                    'progress_data' => $status === 'completed' ? ['notes' => 'تم إكمال النشاط بنجاح'] : null,
                ]);

                $progressCreated++;
            }

            $this->command->info("   ✓ تم إنشاء تقدم للمستخدم: {$user->name}");
        }

        $this->command->info("\n✅ تم إنشاء {$progressCreated} سجل تقدم بنجاح!");
        
        $this->command->info("📊 إحصائيات التقدم:");
        $this->command->info("   - لم يبدأ: " . ActivityProgress::where('status', 'not_started')->count());
        $this->command->info("   - قيد التنفيذ: " . ActivityProgress::where('status', 'in_progress')->count());
        $this->command->info("   - مكتمل: " . ActivityProgress::where('status', 'completed')->count());
        $this->command->info("   - مقفل: " . ActivityProgress::where('status', 'locked')->count());
    }

    private function getRandomStatus()
    {
        $statuses = ['not_started', 'in_progress', 'completed', 'locked'];
        $weights = [20, 30, 40, 10]; // احتمالات
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return 'not_started';
    }

    private function getProgressPercentage($status)
    {
        return match($status) {
            'not_started' => 0,
            'in_progress' => rand(20, 80),
            'completed' => 100,
            'locked' => 0,
            default => 0
        };
    }
}




