<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramPhase;
use App\Models\ProgramSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على جميع البرامج
        $programs = Program::all();

        if ($programs->isEmpty()) {
            $this->command->warn('⚠️  يجب إنشاء برامج أولاً! قم بتشغيل ProgramSeeder');
            return;
        }

        $sessionsCreated = 0;

        foreach ($programs as $program) {
            // الحصول على مراحل البرنامج
            $phases = ProgramPhase::where('program_id', $program->id)
                                 ->orderBy('phase_order')
                                 ->get();

            if ($phases->isEmpty()) {
                $this->command->warn("   ⚠️  البرنامج {$program->name_ar} لا يحتوي على مراحل! قم بتشغيل ProgramPhaseSeeder أولاً");
                continue;
            }

            // توزيع الجلسات على المراحل
            $sessionCount = rand(5, 8);
            $sessionsPerPhase = ceil($sessionCount / $phases->count());
            $globalSessionOrder = 1;

            foreach ($phases as $phase) {
                $phaseSessions = min($sessionsPerPhase, $sessionCount - ($globalSessionOrder - 1));
                
                for ($i = 1; $i <= $phaseSessions; $i++) {
                    $sessionTitle = $this->getSessionTitle($globalSessionOrder);
                    $sessionGoal = $this->getSessionGoal($globalSessionOrder);
                    
                    ProgramSession::create([
                        'id' => (string) Str::uuid(),
                        'program_id' => $program->id,
                        'phase_id' => $phase->id,
                        'title_ar' => $sessionTitle['ar'],
                        'title_en' => $sessionTitle['en'],
                        'session_order' => $globalSessionOrder,
                        'goal_ar' => $sessionGoal['ar'],
                        'goal_en' => $sessionGoal['en'],
                        'duration' => $this->getSessionDuration($globalSessionOrder),
                        'is_active' => true,
                    ]);

                    $sessionsCreated++;
                    $globalSessionOrder++;
                }
            }

            $this->command->info("   ✓ تم إنشاء {$sessionCount} جلسة للبرنامج: {$program->name_ar}");
        }

        $this->command->info("\n✅ تم إنشاء {$sessionsCreated} جلسة بنجاح!");
        $this->command->info("📊 متوسط عدد الجلسات لكل برنامج: " . round($sessionsCreated / $programs->count(), 1));
    }

    private function getSessionTitle($index)
    {
        $titles = [
            [
                'ar' => 'التعريف بالبرنامج والأهداف',
                'en' => 'Introduction to the Program and Objectives'
            ],
            [
                'ar' => 'فهم المشكلة وتأثيرها',
                'en' => 'Understanding the Problem and Its Impact'
            ],
            [
                'ar' => 'تقنيات التنفس والاسترخاء',
                'en' => 'Breathing and Relaxation Techniques'
            ],
            [
                'ar' => 'التفكير الإيجابي وإعادة الهيكلة',
                'en' => 'Positive Thinking and Restructuring'
            ],
            [
                'ar' => 'إدارة الوقت والتنظيم',
                'en' => 'Time Management and Organization'
            ],
            [
                'ar' => 'مهارات التواصل الفعال',
                'en' => 'Effective Communication Skills'
            ],
            [
                'ar' => 'التعامل مع التحديات',
                'en' => 'Dealing with Challenges'
            ],
            [
                'ar' => 'التقييم والتخطيط للمستقبل',
                'en' => 'Evaluation and Future Planning'
            ],
        ];
        
        return $index <= count($titles) ? $titles[$index - 1] : [
            'ar' => "الجلسة رقم {$index}",
            'en' => "Session Number {$index}"
        ];
    }

    private function getSessionGoal($index)
    {
        $goals = [
            [
                'ar' => 'التعريف بأهداف البرنامج وفوائده المتوقعة',
                'en' => 'Introduction to program objectives and expected benefits'
            ],
            [
                'ar' => 'فهم أساسيات المشكلة وآليات تأثيرها',
                'en' => 'Understanding the basics of the problem and its impact mechanisms'
            ],
            [
                'ar' => 'تعلم تقنيات الاسترخاء والتخلص من التوتر',
                'en' => 'Learning relaxation techniques and stress relief'
            ],
            [
                'ar' => 'تحويل الأفكار السلبية إلى إيجابية',
                'en' => 'Converting negative thoughts into positive ones'
            ],
            [
                'ar' => 'تحسين مهارات التنظيم وإدارة المهام',
                'en' => 'Improving organization and task management skills'
            ],
            [
                'ar' => 'تطوير مهارات التواصل مع الآخرين',
                'en' => 'Developing communication skills with others'
            ],
            [
                'ar' => 'تعلم استراتيجيات التعامل مع المواقف الصعبة',
                'en' => 'Learning strategies for dealing with difficult situations'
            ],
            [
                'ar' => 'تقييم التقدم وتحديد الخطوات المستقبلية',
                'en' => 'Evaluating progress and determining future steps'
            ],
        ];
        
        return $index <= count($goals) ? $goals[$index - 1] : [
            'ar' => "تحقيق أهداف الجلسة رقم {$index}",
            'en' => "Achieving the objectives of session number {$index}"
        ];
    }

    private function getSessionDuration($index)
    {
        // جلسة أولى أطول قليلاً، والأخيرة أطول للتقويم
        if ($index == 1) return 90; // دقيقة ونصف للجلسة الأولى
        if ($index == 8) return 120; // ساعتين للجلسة الأخيرة
        return rand(45, 75); // بين 45 و75 دقيقة للجلسات العادية
    }
}