<?php

use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\CasUser;

/*
|--------------------------------------------------------------------------
| Event Toggle Active - Owner
|--------------------------------------------------------------------------
*/

test('cas user can toggle active status of own event', function (): void {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);
    $event = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
        'cancelled' => false,
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('calendarEvent.toggleActive', [$calendar, $event]));
    $response->assertOk();
    $event->refresh();
    expect($event->cancelled)->toBeTruthy();
});

test('cas user can toggle cancelled event back to active', function (): void {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);
    $event = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
        'cancelled' => true,
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('calendarEvent.toggleActive', [$calendar, $event]));
    $response->assertOk();
    $event->refresh();
    expect($event->cancelled)->toBeFalsy();
});

it('handles gracefully toggling event save fail', function () {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);
    $event = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
        'cancelled' => false,
    ]);

    cas_login_user($cas_user);

    $mockEvent = Mockery::mock($event)->makePartial();
    $mockEvent->shouldReceive('save')->andReturn(false);
    $mockEvent->shouldReceive('resolveRouteBinding')->andReturn($mockEvent);
    $this->app->instance(CalendarEvent::class, $mockEvent);

    $response = $this->post(route('calendarEvent.toggleActive', [$calendar, $event]));
    $response->assertOk();
    $response->assertJson(['success' => false, 'message' => 'Απέτυχε η αποθήκευση της τροποποίησης']);
});

/*
|--------------------------------------------------------------------------
| Event Toggle Active - Non-Owner
|--------------------------------------------------------------------------
*/

test('cas user cannot toggle active status of another users event', function (): void {
    $calendar = Calendar::factory()->create();
    $owner = CasUser::factory()->user()->create([
        'employee_number' => '111111',
    ]);
    $other_user = CasUser::factory()->user()->create([
        'employee_number' => '222222',
    ]);
    $event = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $owner->id,
        'cancelled' => false,
    ]);

    cas_login_user($other_user);

    $response = $this->post(route('calendarEvent.toggleActive', [$calendar, $event]));
    $response->assertOk();
    $event->refresh();
    expect($event->cancelled)->toBeFalsy();
});

/*
|--------------------------------------------------------------------------
| Event Toggle Active - Unauthenticated
|--------------------------------------------------------------------------
*/

test('unauthenticated user cannot toggle event active status', function (): void {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);
    $event = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
        'cancelled' => false,
    ]);

    $response = $this->post(route('calendarEvent.toggleActive', [$calendar, $event]));
    $response->assertRedirect(config('cas.cas_client_service').config('cas.cas_uri'));
    $event->refresh();
    expect($event->cancelled)->toBeFalsy();
});

/*
|--------------------------------------------------------------------------
| Event Delete - Permission Check
|--------------------------------------------------------------------------
*/

test('cas user can delete own event', function (): void {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);
    $event = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
    ]);

    cas_login_user($cas_user);

    $response = $this->delete(route('calendar.deleteEvent', [$calendar, $event]));
    $response->assertOk();
    $this->assertDatabaseMissing('calendar_events', ['id' => $event->id]);
});

test('cas user cannot delete another users event', function (): void {
    $calendar = Calendar::factory()->create(['shared' => true]);
    $owner = CasUser::factory()->user()->create([
        'employee_number' => '111111',
    ]);
    $other_user = CasUser::factory()->user()->create([
        'employee_number' => '222222',
    ]);
    $event = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $owner->id,
    ]);

    cas_login_user($other_user);

    $response = $this->delete(route('calendar.deleteEvent', [$calendar, $event]));
    $response->assertOk();
    $response->assertJson(['success' => false, 'message' => 'Δεν επιτρέπεται η λειτουργία σε αυτόν τον χρήστη']);
    $this->assertDatabaseHas('calendar_events', ['id' => $event->id]);
});

/*
|--------------------------------------------------------------------------
| Add Event - Calendar Association
|--------------------------------------------------------------------------
*/

test('event is correctly associated with calendar and cas user', function (): void {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $this->post(route('calendar.addEvent', $calendar), [
        'title' => 'Associated Event',
        'description' => 'Description',
        'start_date' => '2024-06-01',
        'end_date' => '2024-06-02',
        'location' => 'Location',
        'url' => 'https://example.com',
    ]);

    $this->assertDatabaseHas('calendar_events', [
        'title' => 'Associated Event',
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
    ]);
});
