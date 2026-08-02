<?php

namespace Database\Seeders;

use App\Models\ProgramSession;
use App\Models\SessionHomework;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SessionHomeworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على جميع الجلسات
        $sessions = ProgramSession::all();

        if ($sessions->isEmpty()) {
            $this->command->warn('⚠️  يجب إنشاء جلسات أولاً! قم بتشغيل ProgramSessionSeeder');
            return;
        }

        $homeworkCreated = 0;

        foreach ($sessions as $session) {
            // لكل جلسة ننشئ 1-3 مهام منزلية
            $homeworkCount = rand(1, 3);

            for ($i = 1; $i <= $homeworkCount; $i++) {
                $homeworkData = $this->getHomeworkData($i);
                
                SessionHomework::create([
                    'id' => (string) Str::uuid(),
                    'session_id' => $session->id,
                    'title_ar' => $homeworkData['title_ar'],
                    'title_en' => $homeworkData['title_en'],
                    'description_ar' => $homeworkData['description_ar'],
                    'description_en' => $homeworkData['description_en'],
                    'instructions_ar' => $homeworkData['instructions_ar'],
                    'instructions_en' => $homeworkData['instructions_en'],
                    'is_mandatory' => $i === 1, // أول مهمة إلزامية
                    'completion_type' => $this->getCompletionType($i),
                    'homework_order' => $i,
                    'is_active' => true,
                ]);

                $homeworkCreated++;
            }

            $this->command->info("   ✓ تم إنشاء {$homeworkCount} مهمة للجلسة: {$session->title_ar}");
        }

        $this->command->info("\n✅ تم إنشاء {$homeworkCreated} مهمة منزلية بنجاح!");
        
        $mandatoryHomework = SessionHomework::where('is_mandatory', true)->count();
        $optionalHomework = SessionHomework::where('is_mandatory', false)->count();
        
        $this->command->info("📊 إحصائيات المهام:");
        $this->command->info("   - المهام الإجبارية: {$mandatoryHomework}");
        $this->command->info("   - المهام الاختيارية: {$optionalHomework}");
    }

    private function getHomeworkData($index)
    {
        $homework = [
            [
                'title_ar' => 'تطبيق تقنيات التنفس',
                'title_en' => 'Apply Breathing Techniques',
                'description_ar' => 'مارس تقنيات التنفس التي تعلمتها في الجلسة',
                'description_en' => 'Practice the breathing techniques you learned in the session',
                'instructions_ar' => 'قم بتطبيق تمرين التنفس العميق لمدة 10 دقائق يومياً لمدة أسبوع',
                'instructions_en' => 'Apply deep breathing exercise for 10 minutes daily for a week'
            ],
            [
                'title_ar' => 'كتابة اليوميات',
                'title_en' => 'Journal Writing',
                'description_ar' => 'اكتب عن تجربتك اليومية في تطبيق المهارات',
                'description_en' => 'Write about your daily experience in applying the skills',
                'instructions_ar' => 'اكتب يومياً عن المواقف التي استخدمت فيها المهارات الجديدة',
                'instructions_en' => 'Write daily about situations where you used the new skills'
            ],
            [
                'title_ar' => 'مراقبة الأفكار',
                'title_en' => 'Thought Monitoring',
                'description_ar' => 'راقب أفكارك السلبية وحاول تحويلها',
                'description_en' => 'Monitor your negative thoughts and try to transform them',
                'instructions_ar' => 'سجل 3 أفكار سلبية يومياً وحاول إعادة هيكلتها',
                'instructions_en' => 'Record 3 negative thoughts daily and try to restructure them'
            ],
            [
                'title_ar' => 'تمرين الاسترخاء',
                'title_en' => 'Relaxation Exercise',
                'description_ar' => 'مارس تمرين الاسترخاء العضلي',
                'description_en' => 'Practice muscle relaxation exercise',
                'instructions_ar' => 'استمع لتسجيل الاسترخاء ومارس التمرين يومياً',
                'instructions_en' => 'Listen to the relaxation recording and practice daily'
            ],
        ];
        
        return $index <= count($homework) ? $homework[$index - 1] : [
            'title_ar' => "المهمة رقم {$index}",
            'title_en' => "Homework Number {$index}",
            'description_ar' => "وصف المهمة رقم {$index}",
            'description_en' => "Description of Homework Number {$index}",
            'instructions_ar' => "اتبع التعليمات الخاصة بالمهمة",
            'instructions_en' => "Follow the homework instructions"
        ];
    }

    private function getCompletionType($index)
    {
        $types = ['confirmation', 'text_input', 'file_upload', 'form'];
        
        // أول مهمة تتطلب تأكيد فقط
        if ($index === 1) return 'confirmation';
        
        // باقي المهام متنوعة
        return $types[array_rand($types)];
    }
}




