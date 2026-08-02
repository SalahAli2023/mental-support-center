<?php

namespace Database\Factories;

use App\Models\PsychologicalScale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResultInterpretation>
 */
class ResultInterpretationFactory extends Factory
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
            'min_score' => $this->faker->numberBetween(0, 10),
            'max_score' => $this->faker->numberBetween(11, 30),
            'interpretation_label_ar' => $this->faker->word(),
            'interpretation_label_en' => $this->faker->word(),
            'description_ar' => $this->faker->paragraph(),
            'description_en' => $this->faker->paragraph(),
            'color' => $this->faker->hexColor(),
        ];
    }
}
