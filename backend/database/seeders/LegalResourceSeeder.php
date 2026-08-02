<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LegalResource;
use App\Models\LegalResourceCategory;

class LegalResourceSeeder extends Seeder
{
    public function run(): void
    {
        // الحصول على الفئات
        $family = LegalResourceCategory::where('key', 'family')->first();
        $education = LegalResourceCategory::where('key', 'education')->first();
        $health = LegalResourceCategory::where('key', 'health')->first();
        $employment = LegalResourceCategory::where('key', 'employment')->first();
        $protection = LegalResourceCategory::where('key', 'protection')->first();
        $marriage = LegalResourceCategory::where('key', 'marriage')->first();
        $custody = LegalResourceCategory::where('key', 'custody')->first();
        $domesticViolence = LegalResourceCategory::where('key', 'domestic_violence')->first();
        $womenProtection = LegalResourceCategory::where('key', 'women_protection')->first();
        $childProtection = LegalResourceCategory::where('key', 'child_protection')->first();

        $legalResources = [
            // فئة الأسرة
            [
                'law_type' => 'قانون الأحوال الشخصية',
                'article_number_ar' => 'المادة 1',
                'article_number_en' => 'Article 1',
                'text_ar' => 'للزوجة الحق في المسكن الملائم المستقل الذي تؤمن فيه خصوصيتها وكرامتها.',
                'text_en' => 'The wife has the right to adequate independent housing that ensures her privacy and dignity.',
                'category_id' => $family->id,
            ],
            [
                'law_type' => 'قانون الأحوال الشخصية',
                'article_number_ar' => 'المادة 2',
                'article_number_en' => 'Article 2',
                'text_ar' => 'للأم الحق في حضانة أطفالها حتى سن الخامسة عشرة للذكر والثانية عشرة للأنثى.',
                'text_en' => 'The mother has the right to custody of her children until the age of fifteen for males and twelve for females.',
                'category_id' => $custody->id,
            ],

            // فئة العمل
            [
                'law_type' => 'قانون العمل',
                'article_number_ar' => 'المادة 3',
                'article_number_en' => 'Article 3',
                'text_ar' => 'تحظر المضايقة الجنسية في مكان العمل ويعاقب عليها القانون.',
                'text_en' => 'Sexual harassment in the workplace is prohibited and punishable by law.',
                'category_id' => $employment->id,
            ],
            [
                'law_type' => 'قانون العمل',
                'article_number_ar' => 'المادة 4',
                'article_number_en' => 'Article 4',
                'text_ar' => 'للعاملة الحق في إجازة أمومة مدتها عشرة أسابيع بأجر كامل.',
                'text_en' => 'Female workers have the right to maternity leave of ten weeks with full pay.',
                'category_id' => $employment->id,
            ],

            // فئة الحماية
            [
                'law_type' => 'قانون الحماية من العنف الأسري',
                'article_number_ar' => 'المادة 5',
                'article_number_en' => 'Article 5',
                'text_ar' => 'للمرأة الحق في الحماية من جميع أشكال العنف الأسري والتمييز.',
                'text_en' => 'Women have the right to protection from all forms of domestic violence and discrimination.',
                'category_id' => $protection->id,
            ],
            [
                'law_type' => 'قانون الحماية من العنف الأسري',
                'article_number_ar' => 'المادة 6',
                'article_number_en' => 'Article 6',
                'text_ar' => 'يجب على الجهات المختصة توفير الملاجئ الآمنة للنساء والأطفال ضحايا العنف.',
                'text_en' => 'Competent authorities must provide safe shelters for women and children victims of violence.',
                'category_id' => $protection->id,
            ],

            // فئة الصحة
            [
                'law_type' => 'قانون الصحة',
                'article_number_ar' => 'المادة 7',
                'article_number_en' => 'Article 7',
                'text_ar' => 'للنساء الحق في الرعاية الصحية الشاملة بما في ذلك الصحة الإنجابية.',
                'text_en' => 'Women have the right to comprehensive healthcare including reproductive health.',
                'category_id' => $health->id,
            ],

            // فئة التعليم
            [
                'law_type' => 'قانون التعليم',
                'article_number_ar' => 'المادة 8',
                'article_number_en' => 'Article 8',
                'text_ar' => 'للإناث الحق في التعليم المتساوي مع الذكور في جميع المراحل التعليمية.',
                'text_en' => 'Females have the right to equal education with males at all educational levels.',
                'category_id' => $education->id,
            ],

            // فئة الزواج
            [
                'law_type' => 'قانون الأحوال الشخصية',
                'article_number_ar' => 'المادة 9',
                'article_number_en' => 'Article 9',
                'text_ar' => 'لا يتم عقد الزواج إلا برضا الطرفين رضاءً كاملاً لا إكراه فيه.',
                'text_en' => 'Marriage is only concluded with the full and free consent of both parties.',
                'category_id' => $marriage->id,
            ],

            // فئة حماية الطفل
            [
                'law_type' => 'قانون حماية الطفل',
                'article_number_ar' => 'المادة 10',
                'article_number_en' => 'Article 10',
                'text_ar' => 'يحظر تشغيل الأطفال قبل بلوغ سن الخامسة عشرة.',
                'text_en' => 'Employing children under the age of fifteen is prohibited.',
                'category_id' => $childProtection->id,
            ],

            // فئة العنف الأسري
            [
                'law_type' => 'قانون مكافحة العنف الأسري',
                'article_number_ar' => 'المادة 11',
                'article_number_en' => 'Article 11',
                'text_ar' => 'يعتبر العنف الأسري جريمة يعاقب عليها القانون ولا تسقط بالتقادم.',
                'text_en' => 'Domestic violence is considered a crime punishable by law and does not expire by statute of limitations.',
                'category_id' => $domesticViolence->id,
            ],

            // فئة حماية المرأة
            [
                'law_type' => 'قانون حماية المرأة',
                'article_number_ar' => 'المادة 12',
                'article_number_en' => 'Article 12',
                'text_ar' => 'تضمن الدولة للمرأة الحماية القانونية والاجتماعية في جميع المجالات.',
                'text_en' => 'The state guarantees women legal and social protection in all fields.',
                'category_id' => $womenProtection->id,
            ],

            // المزيد من الأمثلة
            [
                'law_type' => 'قانون الجنسية',
                'article_number_ar' => 'المادة 13',
                'article_number_en' => 'Article 13',
                'text_ar' => 'للأطفال الحق في الحصول على جنسية أمهم في الحالات التي ينص عليها القانون.',
                'text_en' => 'Children have the right to acquire their mother\'s nationality in cases stipulated by law.',
                'category_id' => $family->id,
            ],
            [
                'law_type' => 'قانون العقوبات',
                'article_number_ar' => 'المادة 14',
                'article_number_en' => 'Article 14',
                'text_ar' => 'تعتبر جرائم الشرف جرائم قتل يعاقب عليها القانون بأقصى العقوبات.',
                'text_en' => 'Honor crimes are considered murder crimes punishable by the maximum penalties.',
                'category_id' => $protection->id,
            ],
            [
                'law_type' => 'قانون الضمان الاجتماعي',
                'article_number_ar' => 'المادة 15',
                'article_number_en' => 'Article 15',
                'text_ar' => 'للنساء الحق في المعاش التقاعدي والضمان الاجتماعي بشكل متساو مع الرجال.',
                'text_en' => 'Women have the right to pensions and social security equally with men.',
                'category_id' => $employment->id,
            ]
        ];

        foreach ($legalResources as $resource) {
            LegalResource::create($resource);
        }

        $this->command->info('تم إضافة ' . count($legalResources) . ' مورد قانوني بنجاح.');
    }
}