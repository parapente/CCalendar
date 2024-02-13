<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CasUser>
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
        ];
    }

    public function user(): Factory
    {
        $role = Role::where('name', 'User')->first();
        if (!$role) {
            $role = Role::factory()->create(['name' => 'User']);
        }

        return $this->state(function (array $attributes) use ($role) {
            return [
                'role_id' => $role->id,
            ];
        });
    }

    public function supervisor(): Factory
    {
        $role = Role::where('name', 'Supervisor')->first();
        if (!$role) {
            $role = Role::factory()->create(['name' => 'Supervisor']);
        }

        return $this->state(function (array $attributes) use ($role) {
            return [
                'role_id' => $role->id,
            ];
        });
    }
}
