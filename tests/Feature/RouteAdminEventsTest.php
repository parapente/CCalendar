<?php

use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\CasUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns calendar events when requested', function () {
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $calendar = Calendar::factory()->create();
    $role = Role::factory()->create(['name' => 'User']);
    $cas_user = CasUser::factory()->create([
        'role_id' => $role->id,
        'employee_number' => '111111',
    ]);
    $start_date = '2021-01-01';
    $end_date = '2021-01-02';
    $calendarEvent = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
        'start_date' => $start_date,
        'end_date' => $end_date,
    ]);
    $start_date = '2021-01-01';
    $end_date = '2021-02-01';
    $calendarEvent2 = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
        'start_date' => $start_date,
        'end_date' => $end_date,
    ]);
    $start_date = '2020-12-01';
    $end_date = '2021-01-02';
    $calendarEvent3 = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
        'start_date' => $start_date,
        'end_date' => $end_date,
    ]);
    $start_date = '2020-12-01';
    $end_date = '2020-12-02';
    $calendarEvent4 = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
        'start_date' => $start_date,
        'end_date' => $end_date,
    ]);

    $response = $this->get(route('administrator.events', [
        'year' => 2021,
        'month' => 1,
    ]));
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('administrator.events', [
        'year' => 2021,
        'month' => 1,
    ]));
    $response->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            // Το αποτέλεσμα είναι πίνακας οπότε παίρνουμε το πρώτο αποτέλεσμα
            $json
                ->has(3)
                ->first(fn (AssertableJson $json) =>
                    $json->where('title', $calendarEvent3->title)
                        ->where('description', $calendarEvent3->description)
                        ->etc()
                )
                ->etc()
        );
});
