<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $calendars = \App\Models\Calendar::factory(5)->create();
        foreach ($calendars as $calendar) {
            \App\Models\CalendarEvent::factory(10)->create([
                'calendar_id' => $calendar->id,
            ]);
        }
    }
}
