<?php

use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\CasUser;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

/*
|--------------------------------------------------------------------------
| Admin Calendar Toggle Active
|--------------------------------------------------------------------------
*/

test('admin can toggle active status of a calendar', function (): void {
    $admin = User::factory()->create();
    $calendar = Calendar::factory()->create(['active' => true]);

    $response = $this->actingAs($admin)->post(route('administrator.calendar.toggleActive', $calendar));
    $response->assertOk();
    $calendar->refresh();
    expect($calendar->active)->toBeFalsy();
});

test('admin can toggle active status back on', function (): void {
    $admin = User::factory()->create();
    $calendar = Calendar::factory()->create(['active' => false]);

    $response = $this->actingAs($admin)->post(route('administrator.calendar.toggleActive', $calendar));
    $response->assertOk();
    $calendar->refresh();
    expect($calendar->active)->toBeTruthy();
});

test('unauthenticated user cannot toggle calendar active', function (): void {
    $calendar = Calendar::factory()->create(['active' => true]);

    $response = $this->post(route('administrator.calendar.toggleActive', $calendar));
    $response->assertRedirect(route('login'));
    $calendar->refresh();
    expect($calendar->active)->toBeTruthy();
});

/*
|--------------------------------------------------------------------------
| CAS Users Only See Active Calendars
|--------------------------------------------------------------------------
*/

test('cas user only sees active calendars', function (): void {
    $active_calendar = Calendar::factory()->create(['active' => true, 'name' => 'Active Cal']);
    $inactive_calendar = Calendar::factory()->create(['active' => false, 'name' => 'Inactive Cal']);

    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('calendar.index'));
    $response->assertOk()
        ->assertSee('Active Cal')
        ->assertDontSee('Inactive Cal');
});

/*
|--------------------------------------------------------------------------
| Shared vs Non-Shared Calendar Event Visibility
|--------------------------------------------------------------------------
*/

test('cas user sees events on shared calendars from other users', function (): void {
    $calendar = Calendar::factory()->create(['active' => true, 'shared' => true]);
    $owner = CasUser::factory()->user()->create([
        'employee_number' => '111111',
    ]);
    $viewer = CasUser::factory()->user()->create([
        'employee_number' => '222222',
    ]);

    $event = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $owner->id,
        'start_date' => '2024-01-15',
        'end_date' => '2024-01-16',
    ]);

    cas_login_user($viewer);

    $response = $this->get(route('events', ['year' => 2024, 'month' => 1]));
    $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json->has(1)
            ->first(fn (AssertableJson $json) => $json->where('title', $event->title)->etc()
            )
        );
});

test('cas user does not see events on non-shared calendars from other users', function (): void {
    $calendar = Calendar::factory()->create(['active' => true, 'shared' => false]);
    $owner = CasUser::factory()->user()->create([
        'employee_number' => '111111',
    ]);
    $viewer = CasUser::factory()->user()->create([
        'employee_number' => '222222',
    ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $owner->id,
        'start_date' => '2024-01-15',
        'end_date' => '2024-01-16',
    ]);

    cas_login_user($viewer);

    $response = $this->get(route('events', ['year' => 2024, 'month' => 1]));
    $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json->has(0)
        );
});

test('cas supervisor sees all events regardless of shared setting', function (): void {
    $shared_cal = Calendar::factory()->create(['active' => true, 'shared' => true]);
    $private_cal = Calendar::factory()->create(['active' => true, 'shared' => false]);
    $owner = CasUser::factory()->user()->create([
        'employee_number' => '111111',
    ]);
    $supervisor = CasUser::factory()->supervisor()->create([
        'employee_number' => '999999',
    ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $shared_cal->id,
        'cas_user_id' => $owner->id,
        'start_date' => '2024-01-15',
        'end_date' => '2024-01-16',
    ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $private_cal->id,
        'cas_user_id' => $owner->id,
        'start_date' => '2024-01-20',
        'end_date' => '2024-01-21',
    ]);

    cas_login_user($supervisor);

    $response = $this->get(route('events', ['year' => 2024, 'month' => 1]));
    $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json->has(2)
        );
});

/*
|--------------------------------------------------------------------------
| Inactive Calendar Events Not Shown
|--------------------------------------------------------------------------
*/

test('inactive calendar events are not returned', function (): void {
    $active_calendar = Calendar::factory()->create(['active' => true]);
    $inactive_calendar = Calendar::factory()->create(['active' => false]);
    $cas_user = CasUser::factory()->supervisor()->create([
        'employee_number' => '999999',
    ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $active_calendar->id,
        'cas_user_id' => $cas_user->id,
        'start_date' => '2024-01-15',
        'end_date' => '2024-01-16',
    ]);

    CalendarEvent::factory()->create([
        'calendar_id' => $inactive_calendar->id,
        'cas_user_id' => $cas_user->id,
        'start_date' => '2024-01-20',
        'end_date' => '2024-01-21',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('events', ['year' => 2024, 'month' => 1]));
    $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json->has(1)
        );
});
