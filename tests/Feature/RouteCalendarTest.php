<?php

use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\CasUser;
use App\Models\Role;

test('cas user can view calendar', function() {
    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('calendar.index'))
        ->assertRedirect(config('cas.cas_client_service') . config('cas.cas_uri'));

    $cas_user = CasUser::factory()->create([
        'role_id' => Role::factory()->create(['name' => 'User'])->id,
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('calendar.index'));
    $response->assertOk()
        ->assertSee($cas_user->name);
});

test('cas user can add event to calendar', function() {
    $calendar = Calendar::factory()->create();
    $calendar_event = [
        'title' => 'Test Event',
        'description' => 'Test Description',
        'start_date' => '2020-01-01',
        'end_date' => '2020-01-02',
        'location' => 'Test Location',
        'url' => 'http://example.com'
    ];
    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->post(route('calendar.addEvent', $calendar), $calendar_event)
        ->assertRedirect(config('cas.cas_client_service') . config('cas.cas_uri'));
    $this->assertDatabaseMissing('calendar_events', $calendar_event);

    $cas_user = CasUser::factory()->create([
        'role_id' => Role::factory()->create(['name' => 'User'])->id,
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('calendar.addEvent', $calendar), $calendar_event);
    $response->assertOk();
    $this->assertDatabaseHas('calendar_events', $calendar_event);
});

test('cas user can update calendar event', function() {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->create([
        'role_id' => Role::factory()->create(['name' => 'User'])->id,
        'employee_number' => '123456789',
    ]);
    $calendar_event = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
    ]);

    $updated_calendar_event = [
        'id' => $calendar_event->id,
        'title' => 'Test Event',
        'description' => 'Test Description',
        'start_date' => '2020-01-01',
        'end_date' => '2020-01-02',
        'location' => 'Test Location',
        'url' => 'http://example.com'
    ];
    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->post(route('calendar.addEvent', $calendar), $updated_calendar_event)
        ->assertRedirect(config('cas.cas_client_service') . config('cas.cas_uri'));
    $this->assertDatabaseMissing('calendar_events', $updated_calendar_event);

    cas_login_user($cas_user);

    $response = $this->post(route('calendar.addEvent', $calendar), $updated_calendar_event);
    $response->assertOk();
    $this->assertDatabaseHas('calendar_events', $updated_calendar_event);
});

test('cas user can delete calendar event', function() {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->create([
        'role_id' => Role::factory()->create(['name' => 'User'])->id,
        'employee_number' => '123456789',
    ]);
    $calendar_event = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
    ]);

    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->delete(route('calendar.deleteEvent', [$calendar, $calendar_event]))
        ->assertRedirect(config('cas.cas_client_service') . config('cas.cas_uri'));
    $this->assertDatabaseHas('calendar_events', $calendar_event->toArray());

    cas_login_user($cas_user);

    $response = $this->delete(route('calendar.deleteEvent', [$calendar, $calendar_event]));
    $response->assertOk();
    $this->assertDatabaseMissing('calendar_events', $calendar_event->toArray());
});
