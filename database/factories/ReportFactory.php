<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'type' => 1, // Για την ώρα δεν έχουμε άλλο τύπο αναφοράς
            'options' => json_encode([ // Απαιτούμενες ρυθμίσεις για τον τύπο 1
                'from' => '2021-01-01',
                'to' => '2021-01-31',
            ]),
        ];
    }
}
