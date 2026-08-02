<?php

namespace Database\Seeders;

use App\Models\ResultInterpretation;
use Illuminate\Database\Seeder;

class ResultInterpretationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $interpretations = [
            // تفسيرات مقياس السمات الخمس
            [
                'id' => 'j0k1l2m3-4n5o-6p7q-8r9s-t0u1v2w3x4y5',
                'scale_id' => '4d5e6f7g-8h9i-0j1k-2l3m-n4o5p6q7r8s9',
                'min_score' => 10,
                'max_score' => 25,
                'interpretation_label_ar' => 'منخفض',
                'interpretation_label_en' => 'Low',
                'description_ar' => 'تظهر النتائج مستوى منخفضاً من السمات المقيّسة',
                'description_en' => 'Results show low levels of the measured traits',
                'color' => '#EF4444',
            ],
            [
                'id' => 'k1l2m3n4-5o6p-7q8r-9s0t-u1v2w3x4y5z6',
                'scale_id' => '4d5e6f7g-8h9i-0j1k-2l3m-n4o5p6q7r8s9',
                'min_score' => 26,
                'max_score' => 35,
                'interpretation_label_ar' => 'متوسط',
                'interpretation_label_en' => 'Average',
                'description_ar' => 'تظهر النتائج مستوى متوسطاً من السمات المقيّسة',
                'description_en' => 'Results show average levels of the measured traits',
                'color' => '#F59E0B',
            ],
            [
                'id' => 'l2m3n4o5-6p7q-8r9s-0t1u-v2w3x4y5z6a7',
                'scale_id' => '4d5e6f7g-8h9i-0j1k-2l3m-n4o5p6q7r8s9',
                'min_score' => 36,
                'max_score' => 50,
                'interpretation_label_ar' => 'مرتفع',
                'interpretation_label_en' => 'High',
                'description_ar' => 'تظهر النتائج مستوى مرتفعاً من السمات المقيّسة',
                'description_en' => 'Results show high levels of the measured traits',
                'color' => '#10B981',
            ],
            // تفسيرات مقياس بيك للاكتئاب
            [
                'id' => 'm3n4o5p6-7q8r-9s0t-1u2v-w3x4y5z6a7b8',
                'scale_id' => '5e6f7g8h-9i0j-1k2l-3m4n-o5p6q7r8s9t0',
                'min_score' => 0,
                'max_score' => 13,
                'interpretation_label_ar' => 'لا يوجد اكتئاب',
                'interpretation_label_en' => 'No depression',
                'description_ar' => 'لا تظهر النتائج أي علامات للاكتئاب',
                'description_en' => 'Results show no signs of depression',
                'color' => '#10B981',
            ],
            [
                'id' => 'n4o5p6q7-8r9s-0t1u-2v3w-x4y5z6a7b8c9',
                'scale_id' => '5e6f7g8h-9i0j-1k2l-3m4n-o5p6q7r8s9t0',
                'min_score' => 14,
                'max_score' => 19,
                'interpretation_label_ar' => 'اكتئاب بسيط',
                'interpretation_label_en' => 'Mild depression',
                'description_ar' => 'تظهر النتائج علامات اكتئاب بسيطة',
                'description_en' => 'Results show mild signs of depression',
                'color' => '#F59E0B',
            ],
            [
                'id' => 'o5p6q7r8-9s0t-1u2v-3w4x-y5z6a7b8c9d0',
                'scale_id' => '5e6f7g8h-9i0j-1k2l-3m4n-o5p6q7r8s9t0',
                'min_score' => 20,
                'max_score' => 28,
                'interpretation_label_ar' => 'اكتئاب متوسط',
                'interpretation_label_en' => 'Moderate depression',
                'description_ar' => 'تظهر النتائج علامات اكتئاب متوسطة',
                'description_en' => 'Results show moderate signs of depression',
                'color' => '#FB923C',
            ],
            [
                'id' => 'p6q7r8s9-0t1u-2v3w-4x5y-z6a7b8c9d0e1',
                'scale_id' => '5e6f7g8h-9i0j-1k2l-3m4n-o5p6q7r8s9t0',
                'min_score' => 29,
                'max_score' => 63,
                'interpretation_label_ar' => 'اكتئاب شديد',
                'interpretation_label_en' => 'Severe depression',
                'description_ar' => 'تظهر النتائج علامات اكتئاب شديدة - يوصى بمراجعة أخصائي',
                'description_en' => 'Results show severe signs of depression - professional consultation recommended',
                'color' => '#EF4444',
            ],
        ];

        foreach ($interpretations as $interpretation) {
            ResultInterpretation::create($interpretation);
        }
    }
}