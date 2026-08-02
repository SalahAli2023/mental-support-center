<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'name_ar' => 'برنامج إدارة القلق والتوتر',
                'name_en' => 'Anxiety and Stress Management Program',
                'description_ar' => 'برنامج متكامل لتعلم تقنيات إدارة القلق والتوتر اليومي',
                'description_en' => 'A comprehensive program to learn techniques for managing daily anxiety and stress',
                'target_category_ar' => 'البالغين',
                'target_category_en' => 'Adults',
                'duration' => 30,
                'status' => 'active',
                'scale_id' => null,
                'image_url' => 'https://example.com/images/program1.jpg',
            ],
            [
                'name_ar' => 'برنامج تحسين الثقة بالنفس',
                'name_en' => 'Self-Confidence Improvement Program',
                'description_ar' => 'برنامج تدريبي لبناء وتعزيز الثقة بالنفس',
                'description_en' => 'Training program to build and enhance self-confidence',
                'target_category_ar' => 'الشباب',
                'target_category_en' => 'Youth',
                'duration' => 45,
                'status' => 'active',
                'scale_id' => null,
                'image_url' => 'https://example.com/images/program2.jpg',
            ],
            [
                'name_ar' => 'برنامج تحسين جودة النوم',
                'name_en' => 'Sleep Quality Improvement Program',
                'description_ar' => 'برنامج لتطوير عادات نوم صحية وتحسين جودة النوم',
                'description_en' => 'Program to develop healthy sleep habits and improve sleep quality',
                'target_category_ar' => 'البالغين',
                'target_category_en' => 'Adults',
                'duration' => 60,
                'status' => 'active',
                'scale_id' => null,
                'image_url' => 'https://example.com/images/program3.jpg',
            ],
            [
                'name_ar' => 'برنامج إدارة الغضب',
                'name_en' => 'Anger Management Program',
                'description_ar' => 'تعلم تقنيات السيطرة على الغضب والتعامل مع المواقف الصعبة',
                'description_en' => 'Learn techniques to control anger and deal with difficult situations',
                'target_category_ar' => 'المراهقين',
                'target_category_en' => 'Teenagers',
                'duration' => 40,
                'status' => 'draft',
                'scale_id' => null,
                'image_url' => 'https://example.com/images/program4.jpg',
            ],
            [
                'name_ar' => 'برنامج التخطيط وتحقيق الأهداف',
                'name_en' => 'Goal Planning and Achievement Program',
                'description_ar' => 'برنامج لتعلم مهارات التخطيط وتحديد الأهداف وتحقيقها',
                'description_en' => 'Program to learn planning skills, goal setting, and achievement',
                'target_category_ar' => 'الطلاب',
                'target_category_en' => 'Students',
                'duration' => 50,
                'status' => 'inactive',
                'scale_id' => null,
                'image_url' => 'https://example.com/images/program5.jpg',
            ],
        ];

        foreach ($programs as $programData) {
            Program::create([
                'id' => (string) Str::uuid(),
                'name_ar' => $programData['name_ar'],
                'name_en' => $programData['name_en'],
                'description_ar' => $programData['description_ar'],
                'description_en' => $programData['description_en'],
                'target_category_ar' => $programData['target_category_ar'],
                'target_category_en' => $programData['target_category_en'],
                'duration' => $programData['duration'],
                'status' => $programData['status'],
                'scale_id' => $programData['scale_id'],
                'image_url' => $programData['image_url'],
            ]);
        }

        $this->command->info('✅ تم إنشاء ' . count($programs) . ' برنامج بنجاح!');
        $this->command->info('📊 إحصائيات البرامج:');
        $this->command->info('   - البرامج النشطة: ' . Program::where('status', 'active')->count());
        $this->command->info('   - البرامج المسودة: ' . Program::where('status', 'draft')->count());
        $this->command->info('   - البرامج غير النشطة: ' . Program::where('status', 'inactive')->count());
    }
}