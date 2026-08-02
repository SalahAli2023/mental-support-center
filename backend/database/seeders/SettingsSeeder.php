<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Identity Settings
        $this->createSetting('identity', 'site_name_ar', 'مركز الدراسات الاستراتيجية لدعم المرأة والطفل', 'text');
        $this->createSetting('identity', 'site_name_en', 'Center for Strategic Studies to Support Women and Children', 'text');
        $this->createSetting('identity', 'site_tagline_ar', 'منصة متخصصة في تمكين المجتمع وتقديم برامج دعم نفسي واجتماعي ذات تأثير حقيقي', 'text');
        $this->createSetting('identity', 'site_tagline_en', 'Specialized platform for community empowerment and psychosocial support', 'text');
        $this->createSetting('identity', 'site_logo', null, 'image');
        $this->createSetting('identity', 'site_favicon', null, 'image');

        // 🔹 About Settings - Hero
        $this->createSetting('about', 'hero_title_ar', 'من', 'text');
        $this->createSetting('about', 'hero_title_en', 'About', 'text');
        $this->createSetting('about', 'hero_highlight_ar', 'نحن', 'text');
        $this->createSetting('about', 'hero_highlight_en', 'Us', 'text');
        $this->createSetting('about', 'hero_subtitle_ar', 'نحن نعمل على تمكين المجتمع وتقديم برامج دعم نفسي واجتماعي ذات تأثير حقيقي.', 'text');
        $this->createSetting('about', 'hero_subtitle_en', 'We work to empower the community and provide psychological and social support programs with real impact.', 'text');

        // 🔹 About Settings - Overview Paragraphs
        $this->createSetting('about', 'overview_paragraph_1_ar', 'مركز الدراسات الاستراتيجية لدعم المرأة والطفل - اليمن هو مؤسسة مدنية مستقلة غير ربحية يتمتع بشخصية القانونية مستقلة. تأسس وفق قانون الجمعيات والمؤسسات الأهلية رقم (1) لسنة 2001م ولائحته التنفيذية في 20/12/2018 بموجب ترخيص مكتب وزارة الشئون الاجتماعية والعمل رقم (80/ م) ومقره الرئيسي محافظة تعز.', 'text');
        $this->createSetting('about', 'overview_paragraph_1_en', 'The Strategic Studies Center for Women and Children Support - Yemen is an independent non-profit civil institution with an independent legal personality. It was established according to the Associations and Civil Institutions Law No. (1) of 2001.', 'text');

        $this->createSetting('about', 'overview_paragraph_2_ar', 'ويهدف إلى دعم المرأة والطفل بشكل خاص والفئات الضعيفة والمهمشة على وجه العموم من خلال الدراسات والأبحاث المتخصصة والمساهمة في تطوير الاستراتيجيات والسياسات والرؤى الخاصة بتمكين وتحسين أوضاع المرأة والطفل وكافة الفئات الضعيفة والمهمشة وحمايتهم من العنف والتمييز.', 'text');
        $this->createSetting('about', 'overview_paragraph_2_en', 'It aims to support women and children in particular and vulnerable and marginalized groups in general through specialized studies and research, and to contribute to developing strategies and policies.', 'text');

        $this->createSetting('about', 'overview_paragraph_3_ar', 'وكذلك يعمل من خلال المشاريع والبرامج والأنشطة الموجهة لحماية الحقوق والحريات وتعزيز الديمقراطية والحكم الرشيد وبناء السلام والأمن والتنمية، ودعم الهياكل الرسمية وغير الرسمية التي تعني بحماية ورعاية النساء المعنفات والأطفال الجانحين.', 'text');
        $this->createSetting('about', 'overview_paragraph_3_en', 'It also works through projects, programs and activities aimed at protecting rights and freedoms, promoting democracy and good governance, building peace, security and development.', 'text');

        $this->createSetting('about', 'overview_paragraph_4_ar', 'ويتفاعل المركز مع كافة الأطراف المدنية المحلية والإقليمية والدولية من خلال التنسيق والشراكات التي تساهم في تحقيق أهداف المركز.', 'text');
        $this->createSetting('about', 'overview_paragraph_4_en', 'The center interacts with all local, regional and international civil parties through coordination and partnerships that contribute to achieving the centers goals.', 'text');

        // 🔹 About Settings - Badges (JSON)
        $badges = [
            ['label_ar' => 'مؤسسة مستقلة', 'label_en' => 'Independent Institution'],
            ['label_ar' => 'غير ربحي', 'label_en' => 'Non-Profit'],
            ['label_ar' => 'مرخص رسمياً', 'label_en' => 'Officially Licensed'],
        ];
        $this->createSetting('about', 'badges', json_encode($badges, JSON_UNESCAPED_UNICODE), 'json');
        $this->createSetting('about', 'overview_image', null, 'image');

        // 🔹 About Settings - Objectives (JSON)
        $objectives = [
            ['text_ar' => 'المساهمة في تعزيز الوعي المجتمعي حول قضايا المرأة والطفل في اليمن من خلال البرامج والأنشطة واللقاءات والمطبوعات الموجهة', 'text_en' => 'Contribute to enhancing community awareness about women and children issues in Yemen through targeted programs, activities, meetings, and publications'],
            ['text_ar' => 'المساهمة في دعم المشاركة السياسية والاقتصادية والاجتماعية والثقافية للنساء', 'text_en' => 'Contribute to supporting political, economic, social and cultural participation of women'],
            ['text_ar' => 'العمل على تعزيز طرق الحماية للنساء والاطفال من خلال دعم مراكز الرعاية والحماية للنساء المعنفات والاطفال الجانحين', 'text_en' => 'Work to enhance protection methods for women and children by supporting care and protection centers for abused women and delinquent children'],
            ['text_ar' => 'المساهمة في وضع رؤى واستراتيجيات متخصصة تساهم في معالجة التحديات التي تعيق تمكين النساء والأطفال من المشاركة الفعالة وترتقي بوضعهم للأفضل', 'text_en' => 'Contribute to developing specialized visions and strategies that help address challenges hindering the empowerment of women and children for effective participation and improve their situation'],
            ['text_ar' => 'العمل على تعزيز قدرات ومهارات النساء من خلال برامج تدريبية نوعية تمكنها من الانخراط في عملية التنمية والسلام والأمن بفاعلية', 'text_en' => 'Work to enhance the capacities and skills of women through quality training programs that enable them to effectively engage in development, peace and security processes'],
            ['text_ar' => 'المساهمة في تطوير استراتيجيات مواجهة الازمات التي تؤثر على النساء والأطفال من خلال الدراسات والأبحاث والبرامج التي ينفذها المركز', 'text_en' => 'Contribute to developing strategies to confront crises affecting women and children through studies, research and programs implemented by the center'],
            ['text_ar' => 'تعزيز مبادئ وقيم حقوق الإنسان والعدالة والحكم الرشيد من خلال التقارير المتخصصة والبرامج والفعاليات الموجهة لمراقبة ومتابعة حالة حقوق الانسان واليات الحماية المحلية والعدالة الانتقالية والحكم الرشيد', 'text_en' => 'Promote the principles and values of human rights, justice and good governance through specialized reports, programs and events aimed at monitoring and following up on the human rights situation, local protection mechanisms, transitional justice and good governance'],
            ['text_ar' => 'العمل على تفعيل وتطوير اليات الشراكة والتنسيق مع الجهات الرسمية والمجتمع المدني المحلى والإقليمي والدولي والاعلام والمانحين بما لا يتعارض مع أهداف المركز والقوانين النافذة', 'text_en' => 'Work to activate and develop partnership and coordination mechanisms with official authorities, local, regional and international civil society, media and donors in a way that does not conflict with the centers objectives and applicable laws'],
        ];
        $this->createSetting('about', 'objectives', json_encode($objectives, JSON_UNESCAPED_UNICODE), 'json');

        // 🔹 Vision & Mission Settings
        $this->createSetting('vision', 'vision_ar', 'أن نكون في طليعة المجتمع المدني المتخصص في صناعة مستقبل أفضل للنساء والأطفال والفئات الضعيفة في اليمن', 'text');
        $this->createSetting('vision', 'vision_en', 'To be at the forefront of civil society specialized in creating a better future for women, children and vulnerable groups in Yemen', 'text');
        $this->createSetting('vision', 'mission_ar', 'نسعى الى دعم ومساندة المرأة والطفل والفئات الضعيفة والمهمشة من خلال تعزيز قيم المشاركة والحماية والأمن والسلم المجتمعي، وفق رؤى واستراتيجيات ممنهجة من خلال الخبرات والكفاءات المتخصصة للوقاية والحد من آثار العنف والانتهاكات وصولا الى إرساء قيم العدالة والانصاف وسيادة القانون.', 'text');
        $this->createSetting('vision', 'mission_en', 'We seek to support and assist women, children and vulnerable and marginalized groups by promoting the values of participation, protection, security and community peace.', 'text');

        // 🔹 Vision - Values (JSON)
        $values = [
            ['title_ar' => 'العدالة والإنصاف', 'title_en' => 'Justice & Fairness'],
            ['title_ar' => 'الحماية والأمن', 'title_en' => 'Protection & Security'],
            ['title_ar' => 'المشاركة المجتمعية', 'title_en' => 'Community Participation'],
            ['title_ar' => 'الشفافية والنزاهة', 'title_en' => 'Transparency & Integrity'],
        ];
        $this->createSetting('vision', 'values', json_encode($values, JSON_UNESCAPED_UNICODE), 'json');

        // 🔹 Achievements - Stats (JSON)
        $stats = [
            ['label_ar' => 'جلسة استشارية', 'label_en' => 'Consultation Sessions', 'value' => '5000+'],
            ['label_ar' => 'ورشة تدريبية', 'label_en' => 'Training Workshops', 'value' => '200+'],
            ['label_ar' => 'رضا العملاء', 'label_en' => 'Client Satisfaction', 'value' => '98%'],
            ['label_ar' => 'أخصائي معتمد', 'label_en' => 'Certified Specialists', 'value' => '50+'],
        ];
        $this->createSetting('achievements', 'stats', json_encode($stats, JSON_UNESCAPED_UNICODE), 'json');

        // 🔹 Contact - Testimonials (JSON) - ✅ البيانات الكاملة
        $testimonials = [
            [
                'name_ar' => 'أحمد محمد',
                'name_en' => 'Ahmed Mohammed',
                'text_ar' => 'تجربة رائعة مع الفريق المتخصص، ساعدوني في تخطي أصعب المراحل بحرفية عالية واهتمام حقيقي.',
                'text_en' => 'Amazing experience with the specialized team, they helped me overcome the most difficult stages with high professionalism and genuine care.',
                'rating' => 5,
                'role_ar' => 'مستفيد',
                'role_en' => 'Beneficiary',
                'avatar' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'د. فاطمة علي',
                'name_en' => 'Dr. Fatima Ali',
                'text_ar' => 'الورش التدريبية ممتازة والمحتوى علمي وعملي، استفدت كثيراً في مجال عملي كأخصائي نفسي.',
                'text_en' => 'The training workshops are excellent and the content is scientific and practical, I benefited a lot in my work as a psychologist.',
                'rating' => 5,
                'role_ar' => 'أخصائي نفسي',
                'role_en' => 'Psychologist',
                'avatar' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'سارة عبدالله',
                'name_en' => 'Sara Abdullah',
                'text_ar' => 'السرية والاحترافية كانتا على أعلى مستوى، أشعر بالأمان والثقة في التعامل مع المنصة.',
                'text_en' => 'Confidentiality and professionalism were at the highest level, I feel safe and confident in dealing with the platform.',
                'rating' => 4,
                'role_ar' => 'مستفيدة',
                'role_en' => 'Beneficiary',
                'avatar' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'خالد الحربي',
                'name_en' => 'Khalid Al-Harbi',
                'text_ar' => 'خدمة مميزة وفريق محترف، ساعدني في تطوير مهاراتي وتحسين أدائي الوظيفي بشكل ملحوظ.',
                'text_en' => 'Distinctive service and professional team, helped me develop my skills and significantly improve my job performance.',
                'rating' => 5,
                'role_ar' => 'مدير',
                'role_en' => 'Manager',
                'avatar' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'نورة السعد',
                'name_en' => 'Nora Al-Saad',
                'text_ar' => 'الدعم المستمر والمتابعة كانت ممتازة، أشكر الفريق على جهودهم المتميزة.',
                'text_en' => 'Continuous support and follow-up were excellent, I thank the team for their outstanding efforts.',
                'rating' => 4,
                'role_ar' => 'معلمة',
                'role_en' => 'Teacher',
                'avatar' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'محمد الشمري',
                'name_en' => 'Mohammed Al-Shammari',
                'text_ar' => 'التجربة فاقت توقعاتي، الخدمة سريعة والمحتوى قيم ومفيد للغاية.',
                'text_en' => 'The experience exceeded my expectations, the service is fast and the content is very valuable and useful.',
                'rating' => 5,
                'role_ar' => 'طالب',
                'role_en' => 'Student',
                'avatar' => null,
                'is_active' => true,
            ],
        ];
        $this->createSetting('achievements', 'testimonials', json_encode($testimonials, JSON_UNESCAPED_UNICODE), 'json');

        // 🔹 Contact Settings
        $this->createSetting('contact', 'phone', '+967 770 000 000', 'text');
        $this->createSetting('contact', 'email', 'info@cs-wc.org', 'text');
        $this->createSetting('contact', 'address_ar', 'تعز، اليمن', 'text');
        $this->createSetting('contact', 'address_en', 'Taiz, Yemen', 'text');
        $this->createSetting('contact', 'facebook', '', 'text');
        $this->createSetting('contact', 'twitter', '', 'text');
        $this->createSetting('contact', 'instagram', '', 'text');
        $this->createSetting('contact', 'youtube', '', 'text');
        $this->createSetting('contact', 'linkedin', '', 'text');  // ✅ إضافة
        $this->createSetting('contact', 'footer_logo', null, 'image');
        $this->createSetting('contact', 'footer_description_ar', 'منصة رائدة في تقديم خدمات الدعم النفسي والاستشارات المتخصصة. نؤمن بأن الصحة النفسية هي أساس الحياة المتوازنة والمستقرة.', 'text');
        $this->createSetting('contact', 'footer_description_en', 'A leading platform in providing psychological support services and specialized consultations. We believe that mental health is the foundation of a balanced and stable life.', 'text');
        $this->createSetting('contact', 'footer_copyright_ar', 'جميع الحقوق محفوظة - مركز الدراسات الاستراتيجية لدعم المرأة والطفل.', 'text');
        $this->createSetting('contact', 'footer_copyright_en', 'All rights reserved - Center for Strategic Studies to Support Women and Children.', 'text');
    }

    private function createSetting(string $group, string $key, $value, string $type): void
    {
        Setting::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}
