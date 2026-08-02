<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\PsychologicalScale;
use App\Models\ResultInterpretation;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAssessment>
 */
class UserAssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // الحصول على مقياس عشوائي نشط
        $scale = PsychologicalScale::where('is_active', true)->inRandomOrder()->first();
        
        if (!$scale) {
            // إذا لم يكن هناك مقاييس، أنشئ واحداً
            $scale = PsychologicalScale::factory()->create();
        }

        // إنشاء إجابات عشوائية
        $assessmentData = $this->generateRandomAnswers($scale);

        // حساب النتيجة الإجمالية
        $totalScore = $this->calculateTotalScore($assessmentData);

        // الحصول على التفسير المناسب
        $interpretation = ResultInterpretation::where('scale_id', $scale->id)
            ->where('min_score', '<=', $totalScore)
            ->where('max_score', '>=', $totalScore)
            ->first();

        // تحديد مستوى التفسير
        $interpretationLevel = 'متوسط';
        if ($interpretation) {
            $interpretationLevel = $interpretation->interpretation_label ?? 'متوسط';
        } else {
            // إذا لم يوجد تفسير، حدد مستوى بناءً على النتيجة
            if ($totalScore <= 10) {
                $interpretationLevel = 'منخفض';
            } elseif ($totalScore <= 20) {
                $interpretationLevel = 'متوسط';
            } elseif ($totalScore <= 30) {
                $interpretationLevel = 'مرتفع';
            } else {
                $interpretationLevel = 'مرتفع جداً';
            }
        }

        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => User::factory(),
            'scale_id' => $scale->id,
            'total_score' => $totalScore,
            'interpretation_level' => $interpretationLevel,
            'assessment_data' => $assessmentData,
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'completed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * إنشاء إجابات عشوائية للمقياس
     */
    private function generateRandomAnswers(PsychologicalScale $scale): array
    {
        $answers = [];
        $questions = $scale->questions()->with('options')->get();

        if ($questions->isEmpty()) {
            // إذا لم يكن هناك أسئلة، أنشئ إجابات وهمية
            return [
                [
                    'question_id' => 'dummy',
                    'question_text' => 'سؤال تجريبي',
                    'selected_option_id' => 'dummy',
                    'selected_option_text' => 'إجابة تجريبية',
                    'score_value' => rand(1, 5),
                    'answered_at' => now()->toISOString(),
                ]
            ];
        }

        foreach ($questions as $question) {
            $options = $question->options;
            
            // التحقق من وجود خيارات
            if ($options->isEmpty()) {
                // إذا لم يكن هناك خيارات، أنشئ إجابة افتراضية
                $answers[] = [
                    'question_id' => $question->id,
                    'question_text' => $question->question_text ?? 'سؤال بدون نص',
                    'selected_option_id' => null,
                    'selected_option_text' => 'إجابة افتراضية',
                    'score_value' => rand(1, 5),
                    'answered_at' => now()->toISOString(),
                ];
                continue;
            }
            
            $randomOption = $options->random();
            
            $answers[] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'selected_option_id' => $randomOption->id,
                'selected_option_text' => $randomOption->option_text,
                'score_value' => $randomOption->score_value ?? rand(1, 5),
                'answered_at' => now()->toISOString(),
            ];
        }

        return $answers;
    }

    /**
     * حساب النتيجة الإجمالية من الإجابات
     */
    private function calculateTotalScore(array $answers): int
    {
        return collect($answers)->sum('score_value');
    }

    /**
     * حالة للتقييمات الحديثة (آخر 7 أيام)
     */
    public function recent(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'completed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            ];
        });
    }

    /**
     * حالة لتقييمات مستخدم معين
     */
    public function forUser(int $userId): Factory
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
            ];
        });
    }

    /**
     * حالة لتقييمات مقياس معين
     */
    public function forScale(string $scaleId): Factory
    {
        return $this->state(function (array $attributes) use ($scaleId) {
            return [
                'scale_id' => $scaleId,
            ];
        });
    }
}
