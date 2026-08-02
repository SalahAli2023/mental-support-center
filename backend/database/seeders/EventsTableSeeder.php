<?php
// database/seeders/EventsTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use Illuminate\Support\Str;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'title_ar' => 'أمسية الصحة النفسية',
                'title_en' => 'Mental Health Evening',
                'type' => 'أمسيات',
                'description_ar' => 'أمسية تفاعلية تتناول موضوع الصحة النفسية وأهميتها في حياتنا اليومية',
                'description_en' => 'An interactive evening discussing mental health and its importance in our daily lives',
                'full_description_ar' => 'تقدم هذه الأمسية محتوى شاملاً حول الصحة النفسية، تشمل جلسات نقاش تفاعلية وورش عمل مصغرة وفرصة للاستشارات الفردية مع مختصين.',
                'full_description_en' => 'This evening offers comprehensive content about mental health, including interactive discussion sessions, mini-workshops, and opportunities for individual consultations with specialists.',
                'date' => now()->addDays(15)->format('Y-m-d'),
                'duration' => 'ساعتان',
                'location_ar' => 'الرياض - مركز المؤتمرات',
                'location_en' => 'Riyadh - Conference Center',
                'topics_ar' => json_encode(['الصحة النفسية', 'التوتر والقلق', 'التوازن النفسي']),
                'topics_en' => json_encode(['Mental Health', 'Stress and Anxiety', 'Psychological Balance']),
                'speakers' => json_encode([
                    ['name' => 'د. أحمد محمد', 'specialization' => 'أخصائي نفسي'],
                    ['name' => 'د. سارة عبدالله', 'specialization' => 'استشاري علاج سلوكي']
                ]),
                'is_published' => true,
            ],
            [
                'title_ar' => 'ورشة إدارة الضغوط',
                'title_en' => 'Stress Management Workshop',
                'type' => 'ورش عمل',
                'description_ar' => 'ورشة عملية لتعلم تقنيات فعالة في إدارة الضغوط اليومية',
                'description_en' => 'A practical workshop to learn effective techniques for managing daily stress',
                'full_description_ar' => 'تقدم هذه الورشة مجموعة من التقنيات العملية والمثبتة علمياً لإدارة الضغوط، تشمل تمارين التنفس والتأمل وإدارة الوقت.',
                'full_description_en' => 'This workshop offers a set of practical, scientifically proven techniques for stress management, including breathing exercises, meditation, and time management.',
                'date' => now()->addDays(10)->format('Y-m-d'),
                'duration' => '3 ساعات',
                'location_ar' => 'جدة - قاعة التدريب',
                'location_en' => 'Jeddah - Training Hall',
                'topics_ar' => json_encode(['إدارة الضغوط', 'التأمل والاسترخاء', 'تنظيم الوقت']),
                'topics_en' => json_encode(['Stress Management', 'Meditation and Relaxation', 'Time Management']),
                'speakers' => json_encode([
                    ['name' => 'د. خالد إبراهيم', 'specialization' => 'مدرب معتمد في إدارة الضغوط']
                ]),
                'is_published' => true,
            ],
            [
                'title_ar' => 'فعالية اليوم العالمي للصحة النفسية',
                'title_en' => 'World Mental Health Day Event',
                'type' => 'فعاليات',
                'description_ar' => 'فعالية توعوية شاملة بمناسبة اليوم العالمي للصحة النفسية',
                'description_en' => 'A comprehensive awareness event on the occasion of World Mental Health Day',
                'full_description_ar' => 'تشمل الفعالية معرضاً توعوياً، جلسات حوارية، ورش عمل، وعروضاً تقديمية من قبل مختصين في مجال الصحة النفسية.',
                'full_description_en' => 'The event includes an awareness exhibition, dialogue sessions, workshops, and presentations by mental health specialists.',
                'date' => now()->addDays(5)->format('Y-m-d'),
                'duration' => '6 ساعات',
                'location_ar' => 'الدمام - الواجهة البحرية',
                'location_en' => 'Dammam - Corniche',
                'topics_ar' => json_encode(['التوعية بالصحة النفسية', 'دعم المجتمع', 'الوقاية من الأمراض النفسية']),
                'topics_en' => json_encode(['Mental Health Awareness', 'Community Support', 'Prevention of Mental Illnesses']),
                'speakers' => json_encode([
                    ['name' => 'د. فاطمة العتيبي', 'specialization' => 'استشاري طب نفسي'],
                    ['name' => 'أ. محمد الشمري', 'specialization' => 'أخصائي علاج نفسي'],
                    ['name' => 'د. نورة القحطاني', 'specialization' => 'باحثة في الصحة النفسية']
                ]),
                'is_published' => true,
            ],
            [
                'title_ar' => 'أمسية التعافي النفسي',
                'title_en' => 'Psychological Recovery Evening',
                'type' => 'أمسيات',
                'description_ar' => 'أمسية مخصصة لدعم عملية التعافي النفسي وإعادة التأهيل',
                'description_en' => 'An evening dedicated to supporting the psychological recovery and rehabilitation process',
                'full_description_ar' => 'تركز هذه الأمسية على تقديم الدعم النفسي والمشورة للأفراد في مرحلة التعافي، مع توفير بيئة آمنة للتواصل وتبادل الخبرات.',
                'full_description_en' => 'This evening focuses on providing psychological support and counseling for individuals in the recovery stage, while providing a safe environment for communication and sharing experiences.',
                'date' => now()->addDays(20)->format('Y-m-d'),
                'duration' => 'ساعتان ونصف',
                'location_ar' => 'الرياض - مركز الدعم النفسي',
                'location_en' => 'Riyadh - Psychological Support Center',
                'topics_ar' => json_encode(['التعافي النفسي', 'الدعم الجماعي', 'إعادة التأهيل']),
                'topics_en' => json_encode(['Psychological Recovery', 'Group Support', 'Rehabilitation']),
                'speakers' => json_encode([
                    ['name' => 'د. ليلى الحربي', 'specialization' => 'استشاري إعادة تأهيل نفسي']
                ]),
                'is_published' => false, // مسودة
            ],
            [
                'title_ar' => 'ورشة الذكاء العاطفي',
                'title_en' => 'Emotional Intelligence Workshop',
                'type' => 'ورش عمل',
                'description_ar' => 'ورشة متخصصة لتنمية مهارات الذكاء العاطفي وتحسين العلاقات',
                'description_en' => 'A specialized workshop to develop emotional intelligence skills and improve relationships',
                'full_description_ar' => 'تساعد هذه الورشة المشاركين على فهم مشاعرهم وإدارتها بشكل أفضل، وتحسين مهارات التواصل والعلاقات الشخصية والمهنية.',
                'full_description_en' => 'This workshop helps participants better understand and manage their emotions, and improve communication skills and personal and professional relationships.',
                'date' => now()->addDays(25)->format('Y-m-d'),
                'duration' => '4 ساعات',
                'location_ar' => 'جدة - مركز التطوير الشخصي',
                'location_en' => 'Jeddah - Personal Development Center',
                'topics_ar' => json_encode(['الذكاء العاطفي', 'إدارة المشاعر', 'تحسين التواصل']),
                'topics_en' => json_encode(['Emotional Intelligence', 'Emotion Management', 'Communication Improvement']),
                'speakers' => json_encode([
                    ['name' => 'د. عبدالله السعد', 'specialization' => 'خبير في التنمية البشرية'],
                    ['name' => 'أ. منى العلي', 'specialization' => 'مدربة معتمدة في الذكاء العاطفي']
                ]),
                'is_published' => true,
            ]
        ];

        foreach ($events as $eventData) {
            // إنشاء slug من العنوان الإنجليزي
            $eventData['slug'] = Str::slug($eventData['title_en']);
            
            // التحقق من عدم تكرار الـ slug
            $counter = 1;
            $originalSlug = $eventData['slug'];
            while (Event::where('slug', $eventData['slug'])->exists()) {
                $eventData['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }

            Event::create($eventData);
        }

        $this->command->info('✅ تم إنشاء ' . count($events) . ' فعالية بنجاح!');
    }
}