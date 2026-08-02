<?php

namespace Database\Seeders;

use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class QuestionOptionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            // خيارات السؤال الأول (السمات الخمس)
            [
                'id' => 'a1b2c3d4-5e6f-7g8h-9i0j-k1l2m3n4o5p6',
                'question_id' => '6f7g8h9i-0j1k-2l3m-4n5o-p6q7r8s9t0u1',
                'option_text_ar' => 'غير موافق بشدة',
                'option_text_en' => 'Strongly disagree',
                'score_value' => 1,
                'option_order' => 1,
            ],
            [
                'id' => 'b2c3d4e5-6f7g-8h9i-0j1k-l2m3n4o5p6q7',
                'question_id' => '6f7g8h9i-0j1k-2l3m-4n5o-p6q7r8s9t0u1',
                'option_text_ar' => 'غير موافق',
                'option_text_en' => 'Disagree',
                'score_value' => 2,
                'option_order' => 2,
            ],
            [
                'id' => 'c3d4e5f6-7g8h-9i0j-1k2l-m3n4o5p6q7r8',
                'question_id' => '6f7g8h9i-0j1k-2l3m-4n5o-p6q7r8s9t0u1',
                'option_text_ar' => 'محايد',
                'option_text_en' => 'Neutral',
                'score_value' => 3,
                'option_order' => 3,
            ],
            [
                'id' => 'd4e5f6g7-8h9i-0j1k-2l3m-n4o5p6q7r8s9',
                'question_id' => '6f7g8h9i-0j1k-2l3m-4n5o-p6q7r8s9t0u1',
                'option_text_ar' => 'موافق',
                'option_text_en' => 'Agree',
                'score_value' => 4,
                'option_order' => 4,
            ],
            [
                'id' => 'e5f6g7h8-9i0j-1k2l-3m4n-o5p6q7r8s9t0',
                'question_id' => '6f7g8h9i-0j1k-2l3m-4n5o-p6q7r8s9t0u1',
                'option_text_ar' => 'موافق بشدة',
                'option_text_en' => 'Strongly agree',
                'score_value' => 5,
                'option_order' => 5,
            ],
            // خيارات السؤال الثالث (بيك للاكتئاب)
            [
                'id' => 'f6g7h8i9-0j1k-2l3m-4n5o-p6q7r8s9t0u1',
                'question_id' => '8h9i0j1k-2l3m-4n5o-6p7q-r8s9t0u1v2w3',
                'option_text_ar' => 'لا أشعر بالحزن',
                'option_text_en' => 'I do not feel sad',
                'score_value' => 0,
                'option_order' => 1,
            ],
            [
                'id' => 'g7h8i9j0-1k2l-3m4n-5o6p-q7r8s9t0u1v2',
                'question_id' => '8h9i0j1k-2l3m-4n5o-6p7q-r8s9t0u1v2w3',
                'option_text_ar' => 'أشعر بالحزن بين الحين والآخر',
                'option_text_en' => 'I feel sad occasionally',
                'score_value' => 1,
                'option_order' => 2,
            ],
            [
                'id' => 'h8i9j0k1-2l3m-4n5o-6p7q-r8s9t0u1v2w3',
                'question_id' => '8h9i0j1k-2l3m-4n5o-6p7q-r8s9t0u1v2w3',
                'option_text_ar' => 'أشعر بالحزن معظم الوقت',
                'option_text_en' => 'I feel sad most of the time',
                'score_value' => 2,
                'option_order' => 3,
            ],
            [
                'id' => 'i9j0k1l2-3m4n-5o6p-7q8r-s9t0u1v2w3x4',
                'question_id' => '8h9i0j1k-2l3m-4n5o-6p7q-r8s9t0u1v2w3',
                'option_text_ar' => 'أشعر بالحزن الشديد باستمرار',
                'option_text_en' => 'I feel extremely sad all the time',
                'score_value' => 3,
                'option_order' => 4,
            ],
        ];

        foreach ($options as $option) {
            QuestionOption::create($option);
        }
    }
}