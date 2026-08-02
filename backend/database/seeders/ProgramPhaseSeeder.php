<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramPhase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramPhaseSeeder extends Seeder
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

        $phasesCreated = 0;

        foreach ($programs as $program) {
            // لكل برنامج ننشئ 3-5 مراحل
            $phaseCount = rand(3, 5);

            for ($i = 1; $i <= $phaseCount; $i++) {
                $phaseData = $this->getPhaseData($i);
                
                ProgramPhase::create([
                    'id' => (string) Str::uuid(),
                    'program_id' => $program->id,
                    'name_ar' => $phaseData['name_ar'],
                    'name_en' => $phaseData['name_en'],
                    'description_ar' => $phaseData['description_ar'],
                    'description_en' => $phaseData['description_en'],
                    'phase_order' => $i,
                    'is_active' => true,
                    'is_hidden' => false,
                ]);

                $phasesCreated++;
            }

            $this->command->info("   ✓ تم إنشاء {$phaseCount} مرحلة للبرنامج: {$program->name_ar}");
        }

        $this->command->info("\n✅ تم إنشاء {$phasesCreated} مرحلة بنجاح!");
        $this->command->info("📊 متوسط عدد المراحل لكل برنامج: " . round($phasesCreated / $programs->count(), 1));
    }

    private function getPhaseData($index)
    {
        $phases = [
            [
                'name_ar' => 'مرحلة التأسيس والوعي',
                'name_en' => 'Foundation and Awareness Phase',
                'description_ar' => 'مرحلة التعريف بالمشكلة وفهمها بشكل عميق',
                'description_en' => 'Phase of identifying and understanding the problem in depth'
            ],
            [
                'name_ar' => 'مرحلة التعلم والممارسة',
                'name_en' => 'Learning and Practice Phase',
                'description_ar' => 'مرحلة تعلم التقنيات والمهارات الجديدة',
                'description_en' => 'Phase of learning new techniques and skills'
            ],
            [
                'name_ar' => 'مرحلة التطبيق والتكامل',
                'name_en' => 'Application and Integration Phase',
                'description_ar' => 'مرحلة تطبيق المهارات في الحياة اليومية',
                'description_en' => 'Phase of applying skills in daily life'
            ],
            [
                'name_ar' => 'مرحلة التعزيز والاستمرارية',
                'name_en' => 'Reinforcement and Continuity Phase',
                'description_ar' => 'مرحلة تعزيز المهارات المكتسبة وضمان الاستمرارية',
                'description_en' => 'Phase of reinforcing acquired skills and ensuring continuity'
            ],
            [
                'name_ar' => 'مرحلة التقييم والمتابعة',
                'name_en' => 'Evaluation and Follow-up Phase',
                'description_ar' => 'مرحلة تقييم التقدم ووضع خطة للمستقبل',
                'description_en' => 'Phase of evaluating progress and setting future plans'
            ],
        ];
        
        return $index <= count($phases) ? $phases[$index - 1] : [
            'name_ar' => "المرحلة رقم {$index}",
            'name_en' => "Phase Number {$index}",
            'description_ar' => "وصف المرحلة رقم {$index}",
            'description_en' => "Description of Phase Number {$index}"
        ];
    }
}




