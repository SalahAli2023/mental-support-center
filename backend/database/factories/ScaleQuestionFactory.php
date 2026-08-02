<?php

namespace Database\Factories;

use App\Models\PsychologicalScale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScaleQuestion>
 */
class ScaleQuestionFactory extends Factory
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
            'scale_id' => PsychologicalScale::factory(),
            'question_text_ar' => $this->faker->sentence(10),
            'question_text_en' => $this->faker->sentence(10),
            'question_order' => $this->faker->numberBetween(1, 20),
        ];
    }
}
