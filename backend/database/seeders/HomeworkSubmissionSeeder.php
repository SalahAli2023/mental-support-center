<?php

namespace Database\Seeders;

use App\Models\HomeworkSubmission;
use App\Models\SessionHomework;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HomeworkSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على المستخدمين والمهام
        $users = User::where('role', '!=', 'Admin')->take(5)->get();
        $homework = SessionHomework::all();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  يجب إنشاء مستخدمين أولاً!');
            return;
        }

        if ($homework->isEmpty()) {
            $this->command->warn('⚠️  يجب إنشاء مهام منزلية أولاً! قم بتشغيل SessionHomeworkSeeder');
            return;
        }

        $submissionsCreated = 0;

        // لكل مستخدم، ننشئ تسليمات لبعض المهام
        foreach ($users as $user) {
            // نختار 5-10 مهمة عشوائية
            $selectedHomework = $homework->random(min(10, $homework->count()));
            
            foreach ($selectedHomework as $homeworkItem) {
                $status = $this->getRandomStatus();
                
                HomeworkSubmission::create([
                    'id' => (string) Str::uuid(),
                    'homework_id' => $homeworkItem->id,
                    'user_id' => $user->id,
                    'status' => $status,
                    'submission_text' => $status !== 'pending' ? $this->getSubmissionText($homeworkItem->completion_type) : null,
                    'submission_data' => $homeworkItem->completion_type === 'form' ? ['answers' => ['answer1' => 'نص الإجابة']] : null,
                    'submitted_at' => $status !== 'pending' ? now()->subDays(rand(1, 20)) : null,
                    'completed_at' => in_array($status, ['completed', 'approved']) ? now()->subDays(rand(1, 10)) : null,
                ]);

                $submissionsCreated++;
            }

            $this->command->info("   ✓ تم إنشاء تسليمات للمستخدم: {$user->name}");
        }

        $this->command->info("\n✅ تم إنشاء {$submissionsCreated} تسليم بنجاح!");
        
        $this->command->info("📊 إحصائيات التسليمات:");
        $this->command->info("   - معلق: " . HomeworkSubmission::where('status', 'pending')->count());
        $this->command->info("   - تم التسليم: " . HomeworkSubmission::where('status', 'submitted')->count());
        $this->command->info("   - مكتمل: " . HomeworkSubmission::where('status', 'completed')->count());
        $this->command->info("   - موافق عليه: " . HomeworkSubmission::where('status', 'approved')->count());
    }

    private function getRandomStatus()
    {
        $statuses = ['pending', 'submitted', 'completed', 'approved'];
        $weights = [10, 20, 50, 20]; // احتمالات
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return 'pending';
    }

    private function getSubmissionText($completionType)
    {
        $texts = [
            'confirmation' => 'تم إكمال المهمة بنجاح',
            'text_input' => 'قمت بتطبيق التمارين المطلوبة يومياً لمدة أسبوع. لاحظت تحسناً ملحوظاً في قدرتي على إدارة التوتر.',
            'file_upload' => 'تم رفع الملف المطلوب',
            'form' => 'تم ملء النموذج'
        ];
        
        return $texts[$completionType] ?? 'تم إكمال المهمة';
    }
}




