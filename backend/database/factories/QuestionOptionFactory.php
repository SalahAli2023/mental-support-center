<?php

namespace Database\Factories;

use App\Models\ScaleQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionOption>
 */
class QuestionOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'question_id' => ScaleQuestion::factory(),
            'option_text_ar' => $this->faker->word(),
            'option_text_en' => $this->faker->word(),
            'score_value' => $this->faker->numberBetween(0, 4),
            'option_order' => $this->faker->numberBetween(0, 5),
        ];
    }
}
