<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Therapist;
use App\Models\TherapistQualification;

class TherapistQualificationSeeder extends Seeder
{
    public function run(): void
    {
        $therapists = Therapist::all();

        $qualifications = [
            [
                'name_ar' => 'بكالوريوس علم النفس',
                'name_en' => 'Bachelor of Psychology',
                'institution_ar' => 'جامعة الملك سعود',
                'institution_en' => 'King Saud University',
                'year' => 2010
            ],
            [
                'name_ar' => 'ماجستير العلاج النفسي',
                'name_en' => 'Master of Psychotherapy',
                'institution_ar' => 'جامعة القاهرة',
                'institution_en' => 'Cairo University',
                'year' => 2015
            ],
            [
                'name_ar' => 'دكتوراه في علم النفس السريري',
                'name_en' => 'PhD in Clinical Psychology',
                'institution_ar' => 'جامعة هارفارد',
                'institution_en' => 'Harvard University',
                'year' => 2020
            ]
        ];

        foreach ($therapists as $therapist) {
            $qualificationCount = rand(2, 4);
            
            for ($i = 0; $i < $qualificationCount; $i++) {
                $qualification = $qualifications[array_rand($qualifications)];
                
                TherapistQualification::create(array_merge($qualification, [
                    'therapist_id' => $therapist->id,
                    'year' => $qualification['year'] - rand(0, 5)
                ]));
            }
        }
    }
}