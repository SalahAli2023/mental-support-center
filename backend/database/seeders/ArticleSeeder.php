<?php
// database/seeders/ArticleSeeder.php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على أول مستخدم ككاتب
        $author = User::first();
        


        $categories = ArticleCategory::all();
        
        $articles = [
            [
                'title_ar' => 'كيفية التعامل مع القلق اليومي',
                'title_en' => 'How to Deal with Daily Anxiety',
                'excerpt_ar' => 'دليل عملي للتعامل مع مشاعر القلق والتوتر في الحياة اليومية',
                'excerpt_en' => 'A practical guide to dealing with feelings of anxiety and stress in daily life',
                'content_ar' => '<p>القلق هو استجابة طبيعية للضغوط، ولكن عندما يصبح مزمناً يمكن أن يؤثر سلباً على جودة الحياة. في هذا المقال سنستعرض بعض الاستراتيجيات الفعالة للتعامل مع القلق اليومي:</p>
                <ul>
                    <li>ممارسة تمارين التنفس العميق</li>
                    <li>تحديد الأفكار السلبية وتحديها</li>
                    <li>ممارسة الرياضة بانتظام</li>
                    <li>الحفاظ على نظام نوم صحي</li>
                </ul>',
                'content_en' => '<p>Anxiety is a natural response to stress, but when it becomes chronic it can negatively affect quality of life. In this article we will review some effective strategies for dealing with daily anxiety:</p>
                <ul>
                    <li>Practicing deep breathing exercises</li>
                    <li>Identifying and challenging negative thoughts</li>
                    <li>Exercising regularly</li>
                    <li>Maintaining a healthy sleep routine</li>
                </ul>',
                'introduction_ar' => 'القلق من أكثر المشكلات النفسية انتشاراً في عصرنا الحالي، فكيف يمكننا التعامل معه بفعالية؟',
                'introduction_en' => 'Anxiety is one of the most common psychological problems in our modern era, so how can we deal with it effectively?',
                'image' => 'articles/anxiety-article.jpg',
                'attachments' => json_encode(['guide.pdf', 'worksheet.docx']),
                'published_at' => now()->subDays(5),
                'is_published' => true,
                'views' => 150,
            ],
            [
                'title_ar' => 'أساليب فعالة لتربية الأطفال',
                'title_en' => 'Effective Parenting Techniques',
                'excerpt_ar' => 'استراتيجيات مثبتة علمياً لتربية أطفال أصحاء نفسياً واجتماعياً',
                'excerpt_en' => 'Scientifically proven strategies for raising psychologically and socially healthy children',
                'content_ar' => '<p>تربية الأطفال تحتاج إلى توازن بين الحب والانضباط. فيما يلي بعض الأساليب الفعالة:</p>
                <ul>
                    <li>التواصل الإيجابي مع الطفل</li>
                    <li>وضع حدود واضحة ومتسقة</li>
                    <li>تعزيز السلوك الإيجابي</li>
                    <li>أن تكون قدوة حسنة</li>
                </ul>',
                'content_en' => '<p>Parenting requires a balance between love and discipline. Here are some effective techniques:</p>
                <ul>
                    <li>Positive communication with the child</li>
                    <li>Setting clear and consistent boundaries</li>
                    <li>Reinforcing positive behavior</li>
                    <li>Being a good role model</li>
                </ul>',
                'introduction_ar' => 'التربية الفعالة هي فن وعلم يحتاج إلى فهم عمق لاحتياجات الطفل النفسية',
                'introduction_en' => 'Effective parenting is an art and science that requires deep understanding of children\'s psychological needs',
                'image' => 'articles/parenting-article.jpg',
                'attachments' => json_encode(['parenting-guide.pdf']),
                'published_at' => now()->subDays(3),
                'is_published' => true,
                'views' => 89,
            ],
            [
                'title_ar' => 'تقنيات العلاج المعرفي السلوكي',
                'title_en' => 'Cognitive Behavioral Therapy Techniques',
                'excerpt_ar' => 'تعرف على أهم تقنيات العلاج المعرفي السلوكي التي يمكن تطبيقها ذاتياً',
                'excerpt_en' => 'Learn about the most important cognitive behavioral therapy techniques that can be self-applied',
                'content_ar' => '<p>العلاج المعرفي السلوكي هو أحد أكثر أنواع العلاج النفسي فعالية. إليك بعض التقنيات:</p>
                <ul>
                    <li>تسجيل الأفكار التلقائية</li>
                    <li>اختبار الأدلة والتفكير البديل</li>
                    <li>التعريض التدريجي</li>
                    <li>تمارين حل المشكلات</li>
                </ul>',
                'content_en' => '<p>Cognitive behavioral therapy is one of the most effective types of psychotherapy. Here are some techniques:</p>
                <ul>
                    <li>Recording automatic thoughts</li>
                    <li>Testing evidence and alternative thinking</li>
                    <li>Gradual exposure</li>
                    <li>Problem-solving exercises</li>
                </ul>',
                'introduction_ar' => 'العلاج المعرفي السلوكي يساعد في تغيير أنماط التفكير والسلوك غير المفيدة',
                'introduction_en' => 'Cognitive behavioral therapy helps change unhelpful thinking and behavior patterns',
                'image' => 'articles/cbt-article.jpg',
                'attachments' => null,
                'published_at' => now()->subDays(1),
                'is_published' => true,
                'views' => 45,
            ],
            [
                'title_ar' => 'كيفية بناء علاقات صحية',
                'title_en' => 'How to Build Healthy Relationships',
                'excerpt_ar' => 'مفاتيح أساسية لبناء علاقات شخصية ومهنية صحية ومستدامة',
                'excerpt_en' => 'Key foundations for building healthy and sustainable personal and professional relationships',
                'content_ar' => '<p>العلاقات الصحية تحتاج إلى مهارات تواصل فعالة وفهم متبادل. من أهم المفاتيح:</p>
                <ul>
                    <li>التواصل الصريح والمباشر</li>
                    <li>الاحترام المتبادل</li>
                    <li>وضع حدود صحية</li>
                    <li>التعاطف والتفهم</li>
                </ul>',
                'content_en' => '<p>Healthy relationships require effective communication skills and mutual understanding. Among the most important keys:</p>
                <ul>
                    <li>Honest and direct communication</li>
                    <li>Mutual respect</li>
                    <li>Setting healthy boundaries</li>
                    <li>Empathy and understanding</li>
                </ul>',
                'introduction_ar' => 'العلاقات الصحية هي أساس السعادة والاستقرار النفسي في حياتنا',
                'introduction_en' => 'Healthy relationships are the foundation of happiness and psychological stability in our lives',
                'image' => 'articles/relationships-article.jpg',
                'attachments' => json_encode(['communication-worksheet.pdf']),
                'published_at' => now(),
                'is_published' => false, // مسودة
                'views' => 0,
            ]
        ];

        foreach ($articles as $articleData) {
            // إنشاء slug من العنوان الإنجليزي
            $articleData['slug'] = Str::slug($articleData['title_en']);
            
            // التحقق من عدم تكرار الـ slug
            $counter = 1;
            $originalSlug = $articleData['slug'];
            while (Article::where('slug', $articleData['slug'])->exists()) {
                $articleData['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }

            // تعيين كاتب ومجالة عشوائية
            $articleData['author_id'] = $author->id;
            $articleData['category_id'] = $categories->random()->id;

            Article::create($articleData);
        }

        $this->command->info('✅ تم إنشاء ' . count($articles) . ' مقالة بنجاح!');
    }
}