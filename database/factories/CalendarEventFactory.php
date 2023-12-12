<?php

namespace Database\Factories;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CalendarEvent>
 */
class CalendarEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $new_date = DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear());
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'start_date' => $new_date,
            'end_date' => $new_date->add(\DateInterval::createFromDateString(
                $this->faker->randomElement(["1 hour", "1 day", "1 week", "1 month", "1 year"])
            )),
            'location' => $this->faker->address,
            'url' => $this->faker->url,
        ];
    }
}
