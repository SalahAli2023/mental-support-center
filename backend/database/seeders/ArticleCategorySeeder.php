<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name_ar' => 'القلق والتوتر', 'name_en' => 'Anxiety & Stress', 'color' => '#9EBF3B'],
            ['name_ar' => 'الاكتئاب', 'name_en' => 'Depression', 'color' => '#D6A29A'],
            ['name_ar' => 'العلاقات', 'name_en' => 'Relationships', 'color' => '#9EBF3B'],
            ['name_ar' => 'التنمية الذاتية', 'name_en' => 'Self Development', 'color' => '#D6A29A'],
            ['name_ar' => 'تربية الأطفال', 'name_en' => 'Parenting', 'color' => '#9EBF3B'],
            ['name_ar' => 'الصدمات', 'name_en' => 'Trauma', 'color' => '#D6A29A'],
            ['name_ar' => 'الإدمان', 'name_en' => 'Addiction', 'color' => '#9EBF3B'],
            ['name_ar' => 'ضغوط العمل', 'name_en' => 'Work Stress', 'color' => '#D6A29A'],
            ['name_ar' => 'العلاج المعرفي السلوكي', 'name_en' => 'Cognitive Behavioral Therapy', 'color' => '#9EBF3B'],
            ['name_ar' => 'الصحة النفسية', 'name_en' => 'Mental Health', 'color' => '#D6A29A'],
        ];

        foreach ($categories as $category) {
            ArticleCategory::create([
                'name_ar' => $category['name_ar'],
                'name_en' => $category['name_en'],
                'slug' => Str::slug($category['name_en']),
                'color' => $category['color'],
            ]);
        }
    }
}


