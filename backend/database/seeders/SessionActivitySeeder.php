<?php

namespace Database\Seeders;

use App\Models\ProgramSession;
use App\Models\SessionActivity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SessionActivitySeeder extends Seeder
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

        $activitiesCreated = 0;

        foreach ($sessions as $session) {
            // لكل جلسة ننشئ 3-6 أنشطة
            $activityCount = rand(3, 6);

            for ($i = 1; $i <= $activityCount; $i++) {
                $isMandatory = $i <= 2; // أول نشاطين إجباريين، الباقي اختياري
                $activityData = $this->getActivityData($i);
                $activityType = $this->getActivityType($i);
                
                SessionActivity::create([
                    'id' => (string) Str::uuid(),
                    'session_id' => $session->id,
                    'name_ar' => $activityData['name_ar'],
                    'name_en' => $activityData['name_en'],
                    'activity_type' => $activityType,
                    'instructions_ar' => $activityData['instructions_ar'],
                    'instructions_en' => $activityData['instructions_en'],
                    'content_ar' => $activityType === 'text' ? $this->getContentText() : null,
                    'content_en' => $activityType === 'text' ? $this->getContentText('en') : null,
                    'media_url' => in_array($activityType, ['video', 'audio']) ? 'https://example.com/media/' . $activityType . '.mp4' : null,
                    'media_type' => in_array($activityType, ['video', 'audio']) ? $activityType : null,
                    'duration_minutes' => $activityType === 'exercise' ? rand(5, 30) : null,
                    'activity_order' => $i,
                    'is_mandatory' => $isMandatory,
                    'is_active' => true,
                ]);

                $activitiesCreated++;
            }

            $this->command->info("   ✓ تم إنشاء {$activityCount} نشاط للجلسة: {$session->title}");
        }

        $this->command->info("\n✅ تم إنشاء {$activitiesCreated} نشاط بنجاح!");
        
        // إحصائيات إضافية
        $totalActivities = SessionActivity::count();
        $mandatoryActivities = SessionActivity::where('is_mandatory', true)->count();
        $optionalActivities = SessionActivity::where('is_mandatory', false)->count();
        
        $this->command->info("📊 إحصائيات الأنشطة:");
        $this->command->info("   - إجمالي الأنشطة: {$totalActivities}");
        $this->command->info("   - الأنشطة الإجبارية: {$mandatoryActivities}");
        $this->command->info("   - الأنشطة الاختيارية: {$optionalActivities}");
        
        // توزيع الأنشطة حسب النوع
        $activityTypes = SessionActivity::select('activity_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('activity_type')
            ->get();
        
        $this->command->info("📋 توزيع الأنشطة حسب النوع:");
        foreach ($activityTypes as $type) {
            $this->command->info("   - {$type->activity_type}: {$type->count}");
        }
    }

    private function getActivityData($index)
    {
        $activities = [
            [
                'name_ar' => 'قراءة المقال التعليمي',
                'name_en' => 'Reading Educational Article',
                'instructions_ar' => 'يرجى قراءة المحتوى بعناية وتدوين النقاط الهامة',
                'instructions_en' => 'Please read the content carefully and note the important points'
            ],
            [
                'name_ar' => 'تمرين التنفس العميق',
                'name_en' => 'Deep Breathing Exercise',
                'instructions_ar' => 'شاهد الفيديو كاملاً ثم أجب على الأسئلة التالية',
                'instructions_en' => 'Watch the entire video then answer the following questions'
            ],
            [
                'name_ar' => 'مشاهدة الفيديو التوضيحي',
                'name_en' => 'Watching Instructional Video',
                'instructions_ar' => 'قم بتنفيذ التمرين حسب التعليمات الموضحة',
                'instructions_en' => 'Perform the exercise according to the instructions shown'
            ],
            [
                'name_ar' => 'الإجابة على الأسئلة',
                'name_en' => 'Answering Questions',
                'instructions_ar' => 'أجب عن جميع الأسئلة بصدق ووضوح',
                'instructions_en' => 'Answer all questions honestly and clearly'
            ],
            [
                'name_ar' => 'تطبيق التمارين العملية',
                'name_en' => 'Applying Practical Exercises',
                'instructions_ar' => 'اكتب تجربتك الشخصية في تطبيق المهارات',
                'instructions_en' => 'Write your personal experience in applying the skills'
            ],
            [
                'name_ar' => 'كتابة اليوميات',
                'name_en' => 'Journal Writing',
                'instructions_ar' => 'حلل الموقف المقدم وقدم حلولاً عملية',
                'instructions_en' => 'Analyze the presented situation and provide practical solutions'
            ],
            [
                'name_ar' => 'التقييم الذاتي',
                'name_en' => 'Self-Assessment',
                'instructions_ar' => 'قم بتحميل الملف وطباعته للاستخدام',
                'instructions_en' => 'Download and print the file for use'
            ],
            [
                'name_ar' => 'مشاركة التجربة',
                'name_en' => 'Sharing Experience',
                'instructions_ar' => 'استمع إلى التسجيل الصوتي في مكان هادئ',
                'instructions_en' => 'Listen to the audio recording in a quiet place'
            ],
            [
                'name_ar' => 'حل الاختبار',
                'name_en' => 'Taking Test',
                'instructions_ar' => 'شارك تجربتك مع المجموعة إن أردت',
                'instructions_en' => 'Share your experience with the group if you wish'
            ],
            [
                'name_ar' => 'كتابة الملاحظات',
                'name_en' => 'Writing Notes',
                'instructions_ar' => 'اكتب خطة عمل للمستقبل بناءً على ما تعلمته',
                'instructions_en' => 'Write an action plan for the future based on what you learned'
            ],
        ];
        
        return $index <= count($activities) ? $activities[$index - 1] : [
            'name_ar' => "النشاط رقم {$index}",
            'name_en' => "Activity Number {$index}",
            'instructions_ar' => 'اتبع التعليمات الخاصة بالنشاط',
            'instructions_en' => 'Follow the activity instructions'
        ];
    }

    private function getActivityType($index)
    {
        $types = ['text', 'audio', 'video', 'form', 'exercise', 'reflection_questions', 'quiz'];
        
        // توزيع أنواع الأنشطة
        if ($index == 1) return 'text';   // أول نشاط نصي
        if ($index == 2) return 'video';  // ثاني نشاط فيديو
        if ($index == 3) return 'exercise';   // ثالث نشاط تمرين
        
        return $types[array_rand($types)]; // باقي الأنشطة عشوائية
    }

    private function getContentText($lang = 'ar')
    {
        $texts = [
            'ar' => 'هذا محتوى تعليمي شامل يشرح المفاهيم الأساسية. يمكنك قراءة هذا المحتوى بعناية وتدوين النقاط المهمة. المحتوى يتضمن معلومات قيمة ستساعدك في فهم الموضوع بشكل أفضل.',
            'en' => 'This is comprehensive educational content that explains the basic concepts. You can read this content carefully and note the important points. The content includes valuable information that will help you understand the topic better.'
        ];
        
        return $texts[$lang];
    }
}