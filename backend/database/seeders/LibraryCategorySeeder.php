<?php

namespace Database\Seeders;

use App\Models\LibraryCategory;
use Illuminate\Database\Seeder;

class LibraryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['key' => 'anxiety', 'name_ar' => 'القلق والتوتر', 'name_en' => 'Anxiety & Stress', 'color' => '#9EBF3B'],
            ['key' => 'depression', 'name_ar' => 'الاكتئاب', 'name_en' => 'Depression', 'color' => '#D6A29A'],
            ['key' => 'relationships', 'name_ar' => 'العلاقات', 'name_en' => 'Relationships', 'color' => '#9EBF3B'],
            ['key' => 'self-development', 'name_ar' => 'التنمية الذاتية', 'name_en' => 'Self Development', 'color' => '#D6A29A'],
            ['key' => 'parenting', 'name_ar' => 'تربية الأطفال', 'name_en' => 'Parenting', 'color' => '#9EBF3B'],
            ['key' => 'trauma', 'name_ar' => 'الصدمات', 'name_en' => 'Trauma', 'color' => '#D6A29A'],
            ['key' => 'addiction', 'name_ar' => 'الإدمان', 'name_en' => 'Addiction', 'color' => '#9EBF3B'],
            ['key' => 'work-stress', 'name_ar' => 'ضغوط العمل', 'name_en' => 'Work Stress', 'color' => '#D6A29A'],
        ];

        foreach ($categories as $category) {
            LibraryCategory::create($category);
        }
    }
}


