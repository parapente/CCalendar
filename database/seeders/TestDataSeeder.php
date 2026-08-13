<?php

namespace Database\Seeders;

use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\CasUser;
use App\Models\Role;
use Faker\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $roles = Role::all(['id'])->map(function ($role) {
            return $role->id;
        })->toArray();
        $employee_numbers = ['111111', '999999'];
        /** @var Collection<int,CasUser> */
        $employees = new Collection;
        foreach ($employee_numbers as $key => $employee_number) {
            $employees->push(CasUser::factory()->create([
                'employee_number' => $employee_number,
                'role_id' => $roles[$key],
            ]));
        }
        $employee_ids = $employees->map(function (CasUser $employee): int {
            return $employee->id;
        });

        $calendars = Calendar::factory(5)->create();
        foreach ($calendars as $calendar) {
            CalendarEvent::factory(10)->create([
                'calendar_id' => $calendar->id,
                'cas_user_id' => $faker->randomElement($employee_ids),
            ]);
        }

    }
}
