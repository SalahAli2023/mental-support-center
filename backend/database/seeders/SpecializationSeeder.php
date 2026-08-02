<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            ['key' => 'anxiety', 'name_ar' => 'القلق والتوتر', 'name_en' => 'Anxiety & Stress'],
            ['key' => 'depression', 'name_ar' => 'الاكتئاب', 'name_en' => 'Depression'],
            ['key' => 'ocd', 'name_ar' => 'الوسواس القهري', 'name_en' => 'OCD'],
            ['key' => 'addiction', 'name_ar' => 'الإدمان', 'name_en' => 'Addiction'],
            ['key' => 'psychosomatic', 'name_ar' => 'الأمراض النفسجسمانية', 'name_en' => 'Psychosomatic Disorders'],
            ['key' => 'confidence', 'name_ar' => 'ضعف الثقة بالنفس', 'name_en' => 'Low Self-Confidence'],
            ['key' => 'teenagers', 'name_ar' => 'مشكلات المراهقين', 'name_en' => 'Teenage Problems'],
            ['key' => 'specialEducation', 'name_ar' => 'التربية الخاصة', 'name_en' => 'Special Education'],
            ['key' => 'therapeutic', 'name_ar' => 'العلاجية', 'name_en' => 'Therapeutic'],
            ['key' => 'various', 'name_ar' => 'مشكلات منوعة', 'name_en' => 'Various Issues'],
        ];

        foreach ($specializations as $specialization) {
            Specialization::create($specialization);
        }
    }
}


