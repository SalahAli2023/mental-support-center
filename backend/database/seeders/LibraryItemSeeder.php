<?php
// database/seeders/LibraryItemSeeder.php

namespace Database\Seeders;

use App\Models\LibraryCategory;
use App\Models\LibraryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LibraryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = LibraryCategory::all();

        $libraryItems = [
            [
                'title_ar' => 'دليل التعامل مع الاكتئاب',
                'title_en' => 'Depression Management Guide',
                'description_ar' => 'دليل شامل لفهم الاكتئاب وطرق التعامل معه',
                'description_en' => 'Comprehensive guide to understanding depression and ways to deal with it',
                'author_ar' => 'د. محمد العلي',
                'author_en' => 'Dr. Mohammed AlAli',
                'type' => 'guide',
                'cover_image' => 'covers/depression-guide.jpg',
                'file_path' => 'files/depression-management.pdf',
                'file_size' => '2.5 MB',
                'pages' => 45,
                'publish_date' => now()->subMonths(2),
                'downloads' => 120,
                'views' => 300,
                'rating' => 4.5,
                'rating_count' => 24,
                'is_new' => false,
                'is_published' => true,
            ],
            [
                'title_ar' => 'القلق: الأسباب والعلاج',
                'title_en' => 'Anxiety: Causes and Treatment',
                'description_ar' => 'كتاب متخصص في فهم أسباب القلق وطرق علاجه',
                'description_en' => 'Specialized book on understanding the causes of anxiety and treatment methods',
                'author_ar' => 'د. سارة أحمد',
                'author_en' => 'Dr. Sara Ahmed',
                'type' => 'book',
                'cover_image' => 'covers/anxiety-book.jpg',
                'file_path' => 'files/anxiety-treatment.pdf',
                'file_size' => '3.1 MB',
                'pages' => 89,
                'publish_date' => now()->subMonth(1),
                'downloads' => 85,
                'views' => 210,
                'rating' => 4.2,
                'rating_count' => 18,
                'is_new' => true,
                'is_published' => true,
            ],
            [
                'title_ar' => 'بحث: تأثير العلاج السلوكي على القلق',
                'title_en' => 'Research: Effect of Behavioral Therapy on Anxiety',
                'description_ar' => 'بحث علمي يدرس تأثير العلاج السلوكي على تقليل أعراض القلق',
                'description_en' => 'Scientific research studying the effect of behavioral therapy on reducing anxiety symptoms',
                'author_ar' => 'د. خالد السعدي',
                'author_en' => 'Dr. Khaled AlSaadi',
                'type' => 'research',
                'cover_image' => 'covers/research-anxiety.jpg',
                'file_path' => 'files/behavioral-therapy-research.pdf',
                'file_size' => '1.8 MB',
                'pages' => 32,
                'publish_date' => now()->subMonths(3),
                'downloads' => 65,
                'views' => 150,
                'rating' => 4.7,
                'rating_count' => 12,
                'is_new' => false,
                'is_published' => true,
            ],
            [
                'title_ar' => 'مقال: تنمية الذكاء العاطفي',
                'title_en' => 'Article: Developing Emotional Intelligence',
                'description_ar' => 'مقال متعمق حول طرق تنمية الذكاء العاطفي وتحسين العلاقات',
                'description_en' => 'In-depth article on ways to develop emotional intelligence and improve relationships',
                'author_ar' => 'أ. فاطمة القحطاني',
                'author_en' => 'Ms. Fatima AlQahtani',
                'type' => 'article',
                'cover_image' => 'covers/emotional-intelligence.jpg',
                'file_path' => 'files/emotional-intelligence-article.pdf',
                'file_size' => '1.2 MB',
                'pages' => 15,
                'publish_date' => now()->subDays(15),
                'downloads' => 95,
                'views' => 180,
                'rating' => 4.3,
                'rating_count' => 20,
                'is_new' => true,
                'is_published' => true,
            ],
            [
                'title_ar' => 'دليل الأبوة الإيجابية',
                'title_en' => 'Positive Parenting Guide',
                'description_ar' => 'دليل عملي لتطبيق مبادئ الأبوة الإيجابية في تربية الأطفال',
                'description_en' => 'Practical guide for applying positive parenting principles in raising children',
                'author_ar' => 'د. نورة الحربي',
                'author_en' => 'Dr. Noura AlHarbi',
                'type' => 'guide',
                'cover_image' => 'covers/parenting-guide.jpg',
                'file_path' => 'files/positive-parenting.pdf',
                'file_size' => '2.7 MB',
                'pages' => 52,
                'publish_date' => now()->subDays(10),
                'downloads' => 110,
                'views' => 250,
                'rating' => 4.6,
                'rating_count' => 28,
                'is_new' => true,
                'is_published' => true,
            ],
            [
                'title_ar' => 'إدارة ضغوط العمل',
                'title_en' => 'Work Stress Management',
                'description_ar' => 'كتاب متخصص في استراتيجيات إدارة ضغوط العمل والحفاظ على الصحة النفسية',
                'description_en' => 'Specialized book on work stress management strategies and maintaining mental health',
                'author_ar' => 'د. عبدالله الشمري',
                'author_en' => 'Dr. Abdullah AlShammari',
                'type' => 'book',
                'cover_image' => 'covers/work-stress.jpg',
                'file_path' => 'files/work-stress-management.pdf',
                'file_size' => '3.5 MB',
                'pages' => 76,
                'publish_date' => now()->subMonths(4),
                'downloads' => 78,
                'views' => 190,
                'rating' => 4.4,
                'rating_count' => 16,
                'is_new' => false,
                'is_published' => true,
            ]
        ];

        foreach ($libraryItems as $itemData) {
            // تعيين فئة عشوائية
            $itemData['category_id'] = $categories->random()->id;

            LibraryItem::create($itemData);
        }

        $this->command->info('✅ تم إنشاء ' . count($libraryItems) . ' عنصر في المكتبة بنجاح!');
    }
}