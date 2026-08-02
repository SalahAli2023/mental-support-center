<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'id' => '1a2b3c4d-5e6f-7g8h-9i0j-k1l2m3n4o5p6',
                'name_ar' => 'مقاييس الشخصية',
                'name_en' => 'Personality Scales',
                'description_ar' => 'مقاييس تقييم السمات الشخصية والأنماط السلوكية',
                'description_en' => 'Scales for assessing personality traits and behavioral patterns',
                'color' => '#3B82F6',
                'is_active' => true,
            ],
            [
                'id' => '2b3c4d5e-6f7g-8h9i-0j1k-l2m3n4o5p6q7',
                'name_ar' => 'مقاييس الاكتئاب والقلق',
                'name_en' => 'Depression & Anxiety Scales',
                'description_ar' => 'مقاييس تشخيص وتقييم الاكتئاب والقلق والاضطرابات المرتبطة',
                'description_en' => 'Scales for diagnosing and assessing depression, anxiety and related disorders',
                'color' => '#EF4444',
                'is_active' => true,
            ],
            [
                'id' => '3c4d5e6f-7g8h-9i0j-1k2l-m3n4o5p6q7r8',
                'name_ar' => 'مقاييس الذكاء والقدرات',
                'name_en' => 'Intelligence & Abilities Scales',
                'description_ar' => 'مقاييس تقييم الذكاء والقدرات المعرفية والمهارات العقلية',
                'description_en' => 'Scales for assessing intelligence, cognitive abilities and mental skills',
                'color' => '#10B981',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}