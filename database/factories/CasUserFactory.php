<?php

namespace Database\Factories;

use App\Models\CasUser;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CasUser>
 */
class CasUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'active' => 1,
        ];
    }

    public function user(): static
    {
        $role = Role::where('name', 'User')->first();
        if (! $role) {
            $role = Role::factory()->create(['name' => 'User']);
        }

        return $this->state(function (array $attributes) use ($role) {
            return [
                'role_id' => $role->id,
            ];
        });
    }

    public function supervisor(): static
    {
        $role = Role::where('name', 'Supervisor')->first();
        if (! $role) {
            $role = Role::factory()->create(['name' => 'Supervisor']);
        }

        return $this->state(function (array $attributes) use ($role) {
            return [
                'role_id' => $role->id,
            ];
        });
    }
}
