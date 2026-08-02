<?php

namespace Database\Seeders;

use App\Models\PsychologicalScale;
use Illuminate\Database\Seeder;

class PsychologicalScalesTableSeeder extends Seeder
{
    public function run(): void
    {
        $scales = [
            [
                'id' => '4d5e6f7g-8h9i-0j1k-2l3m-n4o5p6q7r8s9',
                'category_id' => '1a2b3c4d-5e6f-7g8h-9i0j-k1l2m3n4o5p6',
                'name_ar' => 'مقياس السمات الخمس الكبرى',
                'name_en' => 'Big Five Personality Traits',
                'description_ar' => 'مقياس لتقييم السمات الشخصية الخمس الرئيسية: الانفتاح،الضمير،التوافق،الانبساط،العصابية',
                'description_en' => 'Scale for assessing the five major personality traits: Openness, Conscientiousness, Agreeableness, Extraversion, Neuroticism',
                'image_url' => 'https://example.com/images/big-five.jpg',
                'max_score' => 50,
                'is_active' => true,
            ],
            [
                'id' => '5e6f7g8h-9i0j-1k2l-3m4n-o5p6q7r8s9t0',
                'category_id' => '2b3c4d5e-6f7g-8h9i-0j1k-l2m3n4o5p6q7',
                'name_ar' => 'مقياس بيك للاكتئاب',
                'name_en' => 'Beck Depression Inventory',
                'description_ar' => 'مقياس شائع لتشخيص وتقييم شدة الاكتئاب',
                'description_en' => 'Common scale for diagnosing and assessing depression severity',
                'image_url' => 'https://example.com/images/beck-depression.jpg',
                'max_score' => 63,
                'is_active' => true,
            ],
        ];

        foreach ($scales as $scale) {
            PsychologicalScale::create($scale);
        }
    }
}