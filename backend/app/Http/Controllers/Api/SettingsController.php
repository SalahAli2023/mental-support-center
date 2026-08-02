<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * جلب جميع الإعدادات
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $groups = ['identity', 'about', 'vision', 'achievements', 'contact'];
            $result = [];

            foreach ($groups as $group) {
                $settings = Setting::where('group', $group)->get();
                $groupData = [];

                foreach ($settings as $setting) {
                    // ✅ معالجة آمنة للقيم
                    $value = $this->safeDecode($setting->value, $setting->type);
                    $groupData[$setting->key] = $value;

                    if ($setting->value_ar) {
                        $valueAr = $this->safeDecode($setting->value_ar, $setting->type);
                        $groupData[$setting->key . '_ar'] = $valueAr;
                    }

                    if ($setting->value_en) {
                        $valueEn = $this->safeDecode($setting->value_en, $setting->type);
                        $groupData[$setting->key . '_en'] = $valueEn;
                    }
                }

                $result[$group] = $groupData;
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching settings:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ دالة مساعدة لفك التشفير
    private function safeDecode($value, $type)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($type === 'json' || $type === 'array') {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
            return $value;
        }

        return $value;
    }

    /**
     * حفظ الإعدادات
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'group' => 'required|string|in:identity,about,vision,achievements,contact',
                'settings' => 'required|array',
            ]);

            $group = $validated['group'];
            $settings = $validated['settings'];

            foreach ($settings as $key => $value) {
                // ✅ تحديد النوع تلقائياً
                $type = 'text';

                // ✅ الكشف عن المصفوفات
                if (is_array($value)) {
                    $type = 'json';
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
                // ✅ الكشف عن الصور
                elseif (str_contains($key, 'image') || str_contains($key, 'logo') || str_contains($key, 'icon') || str_contains($key, 'favicon')) {
                    $type = 'image';
                }
                // ✅ الكشف عن النصوص الطويلة
                elseif (strlen($value) > 500) {
                    $type = 'text';
                }
                // ✅ إذا كانت القيمة null أو empty string
                elseif ($value === null || $value === '') {
                    $type = 'text';
                }
                else {
                    $type = 'text';
                }

                // ✅ معالجة الحقول المزدوجة (_ar, _en)
                if (str_ends_with($key, '_ar')) {
                    $baseKey = str_replace('_ar', '', $key);

                    // البحث عن القيمة الإنجليزية
                    $enKey = $baseKey . '_en';
                    $enValue = $settings[$enKey] ?? null;

                    // إذا كانت القيمة الإنجليزية موجودة
                    if ($enValue !== null) {
                        // معالجة المصفوفات
                        if (is_array($value)) {
                            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                        }
                        if (is_array($enValue)) {
                            $enValue = json_encode($enValue, JSON_UNESCAPED_UNICODE);
                        }

                        Setting::updateOrCreate(
                            ['group' => $group, 'key' => $baseKey],
                            [
                                'value_ar' => $value,
                                'value_en' => $enValue,
                                'type' => $type
                            ]
                        );
                    } else {
                        // فقط العربية
                        if (is_array($value)) {
                            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                        }
                        Setting::updateOrCreate(
                            ['group' => $group, 'key' => $baseKey],
                            ['value_ar' => $value, 'type' => $type]
                        );
                    }
                }
                elseif (str_ends_with($key, '_en')) {
                    // يتم التعامل معها في الحالة السابقة
                    continue;
                }
                else {
                    // ✅ حقل عادي - معالجة المصفوفات
                    if (is_array($value)) {
                        $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                        $type = 'json';
                    }

                    Setting::updateOrCreate(
                        ['group' => $group, 'key' => $key],
                        ['value' => $value, 'type' => $type]
                    );
                }
            }

            // ✅ إعادة البيانات المحفوظة
            $savedData = Setting::where('group', $group)->get();
            $result = [];
            foreach ($savedData as $setting) {
                $val = $setting->value;
                if ($setting->type === 'json' || $setting->type === 'array') {
                    $val = json_decode($val, true);
                }
                $result[$setting->key] = $val;

                if ($setting->value_ar) {
                    $valAr = $setting->value_ar;
                    if ($setting->type === 'json' || $setting->type === 'array') {
                        $valAr = json_decode($valAr, true);
                    }
                    $result[$setting->key . '_ar'] = $valAr;
                }

                if ($setting->value_en) {
                    $valEn = $setting->value_en;
                    if ($setting->type === 'json' || $setting->type === 'array') {
                        $valEn = json_decode($valEn, true);
                    }
                    $result[$setting->key . '_en'] = $valEn;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            \Log::error('Error saving settings:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    

    /**
     * إعادة تعيين الإعدادات للافتراضي
     */
    public function reset(Request $request, string $group): JsonResponse
    {
        try {
            // حذف جميع الإعدادات في المجموعة
            Setting::where('group', $group)->delete();

            // إضافة القيم الافتراضية
            $defaults = $this->getDefaultSettings($group);

            foreach ($defaults as $key => $value) {
                if (is_array($value)) {
                    Setting::setValue($group, $key, json_encode($value), 'json');
                } else {
                    Setting::setValue($group, $key, $value);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings reset successfully',
                'data' => Setting::getGroup($group)
            ]);

        } catch (\Exception $e) {
            Log::error('Error resetting settings:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset settings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * رفع صورة
     */
    public function uploadImage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'group' => 'required|string',
                'key' => 'required|string',
            ]);

            $file = $request->file('image');
            $path = $file->store('settings/' . $request->group, 'public');

            // حذف الصورة القديمة إذا وجدت
            $oldSetting = Setting::where('group', $request->group)
                ->where('key', $request->key)
                ->first();

            if ($oldSetting && $oldSetting->value) {
                Storage::disk('public')->delete($oldSetting->value);
            }

            // حفظ المسار الجديد
            Setting::setValue($request->group, $request->key, $path, 'image');

            $url = Storage::disk('public')->url($path);

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => [
                    'url' => $url,
                    'path' => $path
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error uploading image:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * القيم الافتراضية لكل مجموعة
     */
    private function getDefaultSettings(string $group): array
    {
        $defaults = [
            'identity' => [
                'site_name_ar' => 'مركز الدراسات الاستراتيجية لدعم المرأة والطفل',
                'site_name_en' => 'Center for Strategic Studies to Support Women and Children',
                'site_tagline_ar' => 'منصة متخصصة في تمكين المجتمع وتقديم برامج دعم نفسي واجتماعي ذات تأثير حقيقي',
                'site_tagline_en' => 'Specialized platform for community empowerment and psychosocial support',
                'site_logo' => null,
                'site_favicon' => null,
            ],
            'about' => [
                'hero_title_ar' => 'من',
                'hero_title_en' => 'About',
                'hero_highlight_ar' => 'نحن',
                'hero_highlight_en' => 'Us',
                'hero_subtitle_ar' => 'نحن نعمل على تمكين المجتمع وتقديم برامج دعم نفسي واجتماعي ذات تأثير حقيقي.',
                'hero_subtitle_en' => 'We work to empower the community and provide psychological and social support programs with real impact.',
                'overview_paragraph_1_ar' => 'مركز الدراسات الاستراتيجية لدعم المرأة والطفل - اليمن هو مؤسسة مدنية مستقلة غير ربحية يتمتع بشخصية القانونية مستقلة. تأسس وفق قانون الجمعيات والمؤسسات الأهلية رقم (1) لسنة 2001م ولائحته التنفيذية في 20/12/2018 بموجب ترخيص مكتب وزارة الشئون الاجتماعية والعمل رقم (80/ م) ومقره الرئيسي محافظة تعز.',
                'overview_paragraph_1_en' => 'The Strategic Studies Center for Women and Children Support - Yemen is an independent non-profit civil institution with an independent legal personality. It was established according to the Associations and Civil Institutions Law No. (1) of 2001.',
                'overview_paragraph_2_ar' => 'ويهدف إلى دعم المرأة والطفل بشكل خاص والفئات الضعيفة والمهمشة على وجه العموم من خلال الدراسات والأبحاث المتخصصة والمساهمة في تطوير الاستراتيجيات والسياسات والرؤى الخاصة بتمكين وتحسين أوضاع المرأة والطفل وكافة الفئات الضعيفة والمهمشة وحمايتهم من العنف والتمييز.',
                'overview_paragraph_2_en' => 'It aims to support women and children in particular and vulnerable and marginalized groups in general through specialized studies and research, and to contribute to developing strategies and policies.',
                'overview_paragraph_3_ar' => 'وكذلك يعمل من خلال المشاريع والبرامج والأنشطة الموجهة لحماية الحقوق والحريات وتعزيز الديمقراطية والحكم الرشيد وبناء السلام والأمن والتنمية، ودعم الهياكل الرسمية وغير الرسمية التي تعني بحماية ورعاية النساء المعنفات والأطفال الجانحين.',
                'overview_paragraph_3_en' => 'It also works through projects, programs and activities aimed at protecting rights and freedoms, promoting democracy and good governance, building peace, security and development.',
                'overview_paragraph_4_ar' => 'ويتفاعل المركز مع كافة الأطراف المدنية المحلية والإقليمية والدولية من خلال التنسيق والشراكات التي تساهم في تحقيق أهداف المركز.',
                'overview_paragraph_4_en' => 'The center interacts with all local, regional and international civil parties through coordination and partnerships that contribute to achieving the centers goals.',
                'badges' => json_encode([
                    ['label_ar' => 'مؤسسة مستقلة', 'label_en' => 'Independent Institution'],
                    ['label_ar' => 'غير ربحي', 'label_en' => 'Non-Profit'],
                    ['label_ar' => 'مرخص رسمياً', 'label_en' => 'Officially Licensed'],
                ]),
                'objectives' => json_encode([
                    ['text_ar' => 'المساهمة في تعزيز الوعي المجتمعي حول قضايا المرأة والطفل في اليمن من خلال البرامج والأنشطة واللقاءات والمطبوعات الموجهة', 'text_en' => 'Contribute to enhancing community awareness about women and children issues in Yemen through targeted programs, activities, meetings, and publications'],
                    ['text_ar' => 'المساهمة في دعم المشاركة السياسية والاقتصادية والاجتماعية والثقافية للنساء', 'text_en' => 'Contribute to supporting political, economic, social and cultural participation of women'],
                    ['text_ar' => 'العمل على تعزيز طرق الحماية للنساء والاطفال من خلال دعم مراكز الرعاية والحماية للنساء المعنفات والاطفال الجانحين', 'text_en' => 'Work to enhance protection methods for women and children by supporting care and protection centers for abused women and delinquent children'],
                    ['text_ar' => 'المساهمة في وضع رؤى واستراتيجيات متخصصة تساهم في معالجة التحديات التي تعيق تمكين النساء والأطفال من المشاركة الفعالة وترتقي بوضعهم للأفضل', 'text_en' => 'Contribute to developing specialized visions and strategies that help address challenges hindering the empowerment of women and children for effective participation and improve their situation'],
                    ['text_ar' => 'العمل على تعزيز قدرات ومهارات النساء من خلال برامج تدريبية نوعية تمكنها من الانخراط في عملية التنمية والسلام والأمن بفاعلية', 'text_en' => 'Work to enhance the capacities and skills of women through quality training programs that enable them to effectively engage in development, peace and security processes'],
                    ['text_ar' => 'المساهمة في تطوير استراتيجيات مواجهة الازمات التي تؤثر على النساء والأطفال من خلال الدراسات والأبحاث والبرامج التي ينفذها المركز', 'text_en' => 'Contribute to developing strategies to confront crises affecting women and children through studies, research and programs implemented by the center'],
                    ['text_ar' => 'تعزيز مبادئ وقيم حقوق الإنسان والعدالة والحكم الرشيد من خلال التقارير المتخصصة والبرامج والفعاليات الموجهة لمراقبة ومتابعة حالة حقوق الانسان واليات الحماية المحلية والعدالة الانتقالية والحكم الرشيد', 'text_en' => 'Promote the principles and values of human rights, justice and good governance through specialized reports, programs and events aimed at monitoring and following up on the human rights situation, local protection mechanisms, transitional justice and good governance'],
                    ['text_ar' => 'العمل على تفعيل وتطوير اليات الشراكة والتنسيق مع الجهات الرسمية والمجتمع المدني المحلى والإقليمي والدولي والاعلام والمانحين بما لا يتعارض مع أهداف المركز والقوانين النافذة', 'text_en' => 'Work to activate and develop partnership and coordination mechanisms with official authorities, local, regional and international civil society, media and donors in a way that does not conflict with the centers objectives and applicable laws'],
                ]),
            ],
            'vision' => [
                'vision_ar' => 'أن نكون في طليعة المجتمع المدني المتخصص في صناعة مستقبل أفضل للنساء والأطفال والفئات الضعيفة في اليمن',
                'vision_en' => 'To be at the forefront of civil society specialized in creating a better future for women, children and vulnerable groups in Yemen',
                'mission_ar' => 'نسعى الى دعم ومساندة المرأة والطفل والفئات الضعيفة والمهمشة من خلال تعزيز قيم المشاركة والحماية والأمن والسلم المجتمعي، وفق رؤى واستراتيجيات ممنهجة من خلال الخبرات والكفاءات المتخصصة للوقاية والحد من آثار العنف والانتهاكات وصولا الى إرساء قيم العدالة والانصاف وسيادة القانون.',
                'mission_en' => 'We seek to support and assist women, children and vulnerable and marginalized groups by promoting the values of participation, protection, security and community peace.',
                'values' => json_encode([
                    ['title_ar' => 'العدالة والإنصاف', 'title_en' => 'Justice & Fairness'],
                    ['title_ar' => 'الحماية والأمن', 'title_en' => 'Protection & Security'],
                    ['title_ar' => 'المشاركة المجتمعية', 'title_en' => 'Community Participation'],
                    ['title_ar' => 'الشفافية والنزاهة', 'title_en' => 'Transparency & Integrity'],
                ]),
            ],
            'achievements' => [
                'stats' => json_encode([
                    ['label_ar' => 'جلسة استشارية', 'label_en' => 'Consultation Sessions', 'value' => '5000+'],
                    ['label_ar' => 'ورشة تدريبية', 'label_en' => 'Training Workshops', 'value' => '200+'],
                    ['label_ar' => 'رضا العملاء', 'label_en' => 'Client Satisfaction', 'value' => '98%'],
                    ['label_ar' => 'أخصائي معتمد', 'label_en' => 'Certified Specialists', 'value' => '50+'],
                ]),
            ],
            'contact' => [
                'phone' => '+967 770 000 000',
                'email' => 'info@cs-wc.org',
                'address_ar' => 'تعز، اليمن',
                'address_en' => 'Taiz, Yemen',
                'facebook' => '',
                'twitter' => '',
                'instagram' => '',
                'youtube' => '',
                'footer_description_ar' => 'منصة رائدة في تقديم خدمات الدعم النفسي والاستشارات المتخصصة. نؤمن بأن الصحة النفسية هي أساس الحياة المتوازنة والمستقرة.',
                'footer_description_en' => 'A leading platform in providing psychological support services and specialized consultations. We believe that mental health is the foundation of a balanced and stable life.',
                'footer_copyright_ar' => 'جميع الحقوق محفوظة - مركز الدراسات الاستراتيجية لدعم المرأة والطفل.',
                'footer_copyright_en' => 'All rights reserved - Center for Strategic Studies to Support Women and Children.',
            ],
        ];

        return $defaults[$group] ?? [];
    }
}
