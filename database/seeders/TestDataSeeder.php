<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        $roles = Role::all(['id'])->map(function ($role) {
            return $role->id;
        })->toArray();
        $employee_numbers = ["111111", "999999"];
        $employees = new Collection();
        foreach ($employee_numbers as $employee_number) {
            $employees->push(\App\Models\CasUser::factory()->create([
                'employee_number' => $employee_number,
                'role_id' => $faker->randomElement($roles),
            ]));
        }
        $employee_ids = $employees->map(function ($employee) {
            return $employee->id;
        });

        $calendars = \App\Models\Calendar::factory(5)->create();
        foreach ($calendars as $calendar) {
            \App\Models\CalendarEvent::factory(10)->create([
                'calendar_id' => $calendar->id,
                'cas_user_id' => $faker->randomElement($employee_ids),
            ]);
        }


    }
}
