<?php

use App\Models\Calendar;
use App\Models\User;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

test('admin can view the calendar overview', function () {
    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get(route('administrator.calendar.overview'));
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('administrator.calendar.overview'));
    $response->assertOk();
});

test('admin can view the calendar list', function () {
    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $calendar = Calendar::factory()->create();
    $response = $this->get(route('administrator.calendar.index'));
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('administrator.calendar.index'));
    $response->assertOk()
        ->assertSee($calendar->name);
});

test('admin can create a new calendar', function () {
    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get(route('administrator.calendar.create'));
    $response->assertRedirect(route('login'));
    $response = $this->post(route('administrator.calendar.store'), [
        'name' => 'Test Calendar',
    ]);
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('administrator.calendar.create'));
    $response = $this->post(route('administrator.calendar.store'), [
        'name' => 'Test Calendar',
        'color' => '#000000',
        'active' => 1
    ]);
    $response->assertRedirect(route('administrator.calendar.index'));
    $this->assertDatabaseHas('calendars', ['name' => 'Test Calendar']);
});

test('admin can edit an existing calendar', function () {
    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $calendar = Calendar::factory()->create();
    $response = $this->get(route('administrator.calendar.edit', $calendar));
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('administrator.calendar.edit', $calendar));
    $response->assertOk();
});

test('admin can update an existing calendar', function () {
    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $calendar = Calendar::factory()->create();
    $response = $this->put(route('administrator.calendar.update', $calendar), [
        'Updated Calendar',
    ]);
    $response->assertRedirect(route('login'));
    expect($calendar->fresh()->name)->not->toBe('Updated Calendar');

    $user = User::factory()->create();
    $response = $this->actingAs($user)->put(route('administrator.calendar.update', $calendar), [
        'name' => 'Updated Calendar',
        'color' => '#000001',
        'active' => 0
    ]);
    $calendar->refresh();
    expect($calendar->name)->toBe('Updated Calendar');
    expect($calendar->color)->toBe('#000001');
    expect($calendar->active)->toBe(0);
});

test('noone can delete an existing calendar', function () {
    expect(fn() => route('administrator.calendar.delete'))
        ->toThrow(Symfony\Component\Routing\Exception\RouteNotFoundException::class);
});

