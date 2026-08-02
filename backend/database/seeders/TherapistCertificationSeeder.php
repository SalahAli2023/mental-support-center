<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Therapist;
use App\Models\TherapistCertification;

class TherapistCertificationSeeder extends Seeder
{
    public function run(): void
    {
        $therapists = Therapist::all();

        $certifications = [
            [
                'name' => 'Certified Cognitive Behavioral Therapist',
                'issuing_authority' => 'American Institute of Cognitive Therapy',
                'year_obtained' => 2018
            ],
            [
                'name' => 'Licensed Professional Counselor',
                'issuing_authority' => 'National Board for Certified Counselors',
                'year_obtained' => 2019
            ],
            [
                'name' => 'Certified Family Therapist',
                'issuing_authority' => 'American Association for Marriage and Family Therapy',
                'year_obtained' => 2020
            ],
            [
                'name' => 'Trauma Therapy Specialist',
                'issuing_authority' => 'International Association of Trauma Professionals',
                'year_obtained' => 2021
            ]
        ];

        foreach ($therapists as $therapist) {
            $certificationCount = rand(1, 3);
            $usedCertifications = [];
            
            for ($i = 0; $i < $certificationCount; $i++) {
                $availableCerts = array_diff_key($certifications, array_flip($usedCertifications));
                
                if (empty($availableCerts)) {
                    break;
                }
                
                $certIndex = array_rand($availableCerts);
                $usedCertifications[] = $certIndex;
                $certification = $certifications[$certIndex];
                
                TherapistCertification::create(array_merge($certification, [
                    'therapist_id' => $therapist->id,
                    'year_obtained' => $certification['year_obtained'] - rand(0, 2)
                ]));
            }
        }
    }
}