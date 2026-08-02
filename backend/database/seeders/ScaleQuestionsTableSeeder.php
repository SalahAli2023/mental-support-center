<?php

namespace Database\Seeders;

use App\Models\ScaleQuestion;
use Illuminate\Database\Seeder;

class ScaleQuestionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            // أسئلة مقياس السمات الخمس الكبرى
            [
                'id' => '6f7g8h9i-0j1k-2l3m-4n5o-p6q7r8s9t0u1',
                'scale_id' => '4d5e6f7g-8h9i-0j1k-2l3m-n4o5p6q7r8s9',
                'question_text_ar' => 'أنا شخص مفعم بالحيوية والنشاط',
                'question_text_en' => 'I am someone who is full of energy and enthusiasm',
                'question_order' => 1,
            ],
            [
                'id' => '7g8h9i0j-1k2l-3m4n-5o6p-q7r8s9t0u1v2',
                'scale_id' => '4d5e6f7g-8h9i-0j1k-2l3m-n4o5p6q7r8s9',
                'question_text_ar' => 'أستمتع بالتجارب والأشياء الجديدة',
                'question_text_en' => 'I enjoy new experiences and things',
                'question_order' => 2,
            ],
            // أسئلة مقياس بيك للاكتئاب
            [
                'id' => '8h9i0j1k-2l3m-4n5o-6p7q-r8s9t0u1v2w3',
                'scale_id' => '5e6f7g8h-9i0j-1k2l-3m4n-o5p6q7r8s9t0',
                'question_text_ar' => 'أشعر بالحزن معظم الوقت',
                'question_text_en' => 'I feel sad most of the time',
                'question_order' => 1,
            ],
            [
                'id' => '9i0j1k2l-3m4n-5o6p-7q8r-s9t0u1v2w3x4',
                'scale_id' => '5e6f7g8h-9i0j-1k2l-3m4n-o5p6q7r8s9t0',
                'question_text_ar' => 'أشعر بعدم القيمة كإنسان',
                'question_text_en' => 'I feel worthless as a person',
                'question_order' => 2,
            ],
        ];

        foreach ($questions as $question) {
            ScaleQuestion::create($question);
        }
    }
}