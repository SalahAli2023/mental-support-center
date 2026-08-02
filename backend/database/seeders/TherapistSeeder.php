<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Therapist;
use App\Models\User;

class TherapistSeeder extends Seeder
{
    public function run(): void
    {
        // الحصول على مستخدمين لديهم دور معالج
        $therapistUsers = User::where('role', 'Therapist')->get();

        if ($therapistUsers->isEmpty()) {
            // إنشاء بعض المستخدمين المعالجين إذا لم يكن هناك أي منهم
            $therapistUsers = User::factory(5)->create([
                'role' => 'Therapist'
            ]);
        }

        $therapists = [
            [
                'name_ar' => 'د. أحمد محمد',
                'name_en' => 'Dr. Ahmed Mohamed',
                'title_ar' => 'أخصائي نفسي',
                'title_en' => 'Psychologist',
                'specialty_ar' => 'العلاج المعرفي السلوكي',
                'specialty_en' => 'Cognitive Behavioral Therapy',
                'methodologies_ar' => ['العلاج المعرفي', 'العلاج السلوكي'],
                'methodologies_en' => ['Cognitive Therapy', 'Behavioral Therapy'],
                'session_duration' => 60,
                'experience' => 8,
                'hourly_rate' => 150.00,
                'gender' => 'male',
                'bio_ar' => 'أخصائي نفسي معتمد مع 8 سنوات من الخبرة',
                'bio_en' => 'Certified psychologist with 8 years of experience',
                'status' => 'active'
            ],
            [
                'name_ar' => 'د. فاطمة علي',
                'name_en' => 'Dr. Fatima Ali',
                'title_ar' => 'معالجة نفسية',
                'title_en' => 'Psychological Therapist',
                'specialty_ar' => 'العلاج الأسري',
                'specialty_en' => 'Family Therapy',
                'methodologies_ar' => ['العلاج الأسري', 'العلاج الجماعي'],
                'methodologies_en' => ['Family Therapy', 'Group Therapy'],
                'session_duration' => 45,
                'experience' => 5,
                'hourly_rate' => 120.00,
                'gender' => 'female',
                'bio_ar' => 'معالجة نفسية متخصصة في العلاج الأسري',
                'bio_en' => 'Psychological therapist specialized in family therapy',
                'status' => 'active'
            ]
        ];

        foreach ($therapistUsers as $index => $user) {
            $therapistData = $therapists[$index % count($therapists)] ?? $therapists[0];
            
            Therapist::create(array_merge($therapistData, [
                'user_id' => $user->id,
                'phone' => '+9665' . rand(10000000, 99999999),
                'date_of_birth' => now()->subYears(rand(30, 50))->format('Y-m-d'),
                'rating' => rand(35, 50) / 10, // تقييم بين 3.5 و 5.0
                'rating_count' => rand(10, 100)
            ]));
        }
    }
}