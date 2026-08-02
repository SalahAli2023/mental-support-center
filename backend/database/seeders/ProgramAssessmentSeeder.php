<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramAssessment;
use App\Models\PsychologicalScale;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramAssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على جميع البرامج
        $programs = Program::all();
        
        // الحصول على المقاييس النفسية
        $scales = PsychologicalScale::all();

        if ($programs->isEmpty()) {
            $this->command->warn('⚠️  يجب إنشاء برامج أولاً! قم بتشغيل ProgramSeeder');
            return;
        }

        if ($scales->isEmpty()) {
            $this->command->warn('⚠️  يجب إنشاء مقاييس نفسية أولاً! قم بتشغيل PsychologicalScalesTableSeeder');
            return;
        }

        $assessmentsCreated = 0;

        foreach ($programs as $program) {
            // لكل برنامج ننشئ تقييم قبلي وبعدي
            $scale = $scales->random();
            
            // تقييم قبلي
            ProgramAssessment::create([
                'id' => (string) Str::uuid(),
                'program_id' => $program->id,
                'scale_id' => $scale->id,
                'assessment_type' => 'pre',
                'is_mandatory' => true,
                'order' => 1,
                'instructions_ar' => 'يرجى الإجابة على جميع الأسئلة بصدق قبل بدء البرنامج',
                'instructions_en' => 'Please answer all questions honestly before starting the program',
                'is_active' => true,
            ]);

            // تقييم بعدي
            ProgramAssessment::create([
                'id' => (string) Str::uuid(),
                'program_id' => $program->id,
                'scale_id' => $scale->id,
                'assessment_type' => 'post',
                'is_mandatory' => true,
                'order' => 2,
                'instructions_ar' => 'يرجى الإجابة على جميع الأسئلة بصدق بعد إكمال البرنامج',
                'instructions_en' => 'Please answer all questions honestly after completing the program',
                'is_active' => true,
            ]);

            $assessmentsCreated += 2;
            $this->command->info("   ✓ تم إنشاء تقييمين للبرنامج: {$program->name_ar}");
        }

        $this->command->info("\n✅ تم إنشاء {$assessmentsCreated} تقييم بنجاح!");
        $this->command->info("📊 إحصائيات التقييمات:");
        $this->command->info("   - التقييمات القبلية: " . ProgramAssessment::where('assessment_type', 'pre')->count());
        $this->command->info("   - التقييمات البعدية: " . ProgramAssessment::where('assessment_type', 'post')->count());
    }
}




