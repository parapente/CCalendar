<?php

use App\Models\Calendar;
use App\Models\CasUser;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Calendar Form Validation
|--------------------------------------------------------------------------
*/

test('admin cannot create calendar without required fields', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('administrator.calendar.store'), []);
    $response->assertSessionHasErrors(['name', 'color', 'active', 'shared']);
});

test('admin cannot create calendar with invalid color', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('administrator.calendar.store'), [
        'name' => 'Test Calendar',
        'color' => 'not-a-color',
        'active' => 1,
        'shared' => 1,
    ]);
    $response->assertSessionHasErrors(['color']);
});

test('admin cannot create calendar with non-boolean active', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('administrator.calendar.store'), [
        'name' => 'Test Calendar',
        'color' => '#000000',
        'active' => 'not-a-bool',
        'shared' => 1,
    ]);
    $response->assertSessionHasErrors(['active']);
});

test('admin cannot create calendar with non-boolean shared', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('administrator.calendar.store'), [
        'name' => 'Test Calendar',
        'color' => '#000000',
        'active' => 1,
        'shared' => 'not-a-bool',
    ]);
    $response->assertSessionHasErrors(['shared']);
});

test('admin can create calendar with valid data', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('administrator.calendar.store'), [
        'name' => 'Valid Calendar',
        'color' => '#FF5733',
        'active' => 1,
        'shared' => 0,
    ]);
    $response->assertRedirect(route('administrator.calendar.index'));
    $this->assertDatabaseHas('calendars', ['name' => 'Valid Calendar']);
});

test('admin cannot update calendar without required fields', function (): void {
    $user = User::factory()->create();
    $calendar = Calendar::factory()->create();

    $response = $this->actingAs($user)->put(route('administrator.calendar.update', $calendar), []);
    $response->assertSessionHasErrors(['name', 'color', 'active', 'shared']);
});

test('admin cannot update calendar with invalid color', function (): void {
    $user = User::factory()->create();
    $calendar = Calendar::factory()->create();

    $response = $this->actingAs($user)->put(route('administrator.calendar.update', $calendar), [
        'name' => 'Updated',
        'color' => 'invalid',
        'active' => 1,
        'shared' => 1,
    ]);
    $response->assertSessionHasErrors(['color']);
});

/*
|--------------------------------------------------------------------------
| Calendar Event Form Validation
|--------------------------------------------------------------------------
*/

test('cas user cannot add event without required fields', function (): void {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('calendar.addEvent', $calendar), []);
    $response->assertSessionHasErrors(['title', 'start_date', 'end_date']);
});

test('cas user cannot add event with invalid url', function (): void {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('calendar.addEvent', $calendar), [
        'title' => 'Test Event',
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-02',
        'url' => 'not-a-url',
    ]);
    $response->assertSessionHasErrors(['url']);
});

test('cas user cannot add event with invalid start_date', function (): void {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('calendar.addEvent', $calendar), [
        'title' => 'Test Event',
        'start_date' => 'not-a-date',
        'end_date' => '2024-01-02',
    ]);
    $response->assertSessionHasErrors(['start_date']);
});

test('cas user cannot add event with invalid end_date', function (): void {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('calendar.addEvent', $calendar), [
        'title' => 'Test Event',
        'start_date' => '2024-01-01',
        'end_date' => 'not-a-date',
    ]);
    $response->assertSessionHasErrors(['end_date']);
});

test('cas user can add event with valid optional fields', function (): void {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('calendar.addEvent', $calendar), [
        'title' => 'Test Event',
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-02',
    ]);
    $response->assertOk();
    $this->assertDatabaseHas('calendar_events', [
        'title' => 'Test Event',
        'location' => '',
        'url' => '',
    ]);
});

/*
|--------------------------------------------------------------------------
| Report Form Validation
|--------------------------------------------------------------------------
*/

test('admin cannot create report without required fields', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('administrator.report.store'), []);
    $response->assertSessionHasErrors(['name', 'type']);
});

test('admin cannot create report without from/to for type 1', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('administrator.report.store'), [
        'name' => 'Test Report',
        'type' => 1,
    ]);
    $response->assertSessionHasErrors(['from', 'to']);
});

test('admin cannot create report with invalid type', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('administrator.report.store'), [
        'name' => 'Test Report',
        'type' => 'not-an-int',
        'from' => '2024-01-01',
        'to' => '2024-01-31',
    ]);
    $response->assertSessionHasErrors(['type']);
});

test('admin cannot create report with invalid date format', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('administrator.report.store'), [
        'name' => 'Test Report',
        'type' => 1,
        'from' => 'not-a-date',
        'to' => '2024-01-31',
    ]);
    $response->assertSessionHasErrors(['from']);
});

test('admin can create report with valid data', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('administrator.report.store'), [
        'name' => 'Valid Report',
        'type' => 1,
        'from' => '2024-01-01',
        'to' => '2024-01-31',
    ]);
    $response->assertRedirect(route('administrator.report.index'));
    $this->assertDatabaseHas('reports', ['name' => 'Valid Report']);
});

test('admin cannot update report without required fields', function (): void {
    $user = User::factory()->create();
    $report = Report::factory()->create();

    $response = $this->actingAs($user)->put(route('administrator.report.update', $report), []);
    $response->assertSessionHasErrors(['name', 'type']);
});

test('admin cannot update report without from/to for type 1', function (): void {
    $user = User::factory()->create();
    $report = Report::factory()->create();

    $response = $this->actingAs($user)->put(route('administrator.report.update', $report), [
        'name' => 'Updated Report',
        'type' => 1,
    ]);
    $response->assertSessionHasErrors(['from', 'to']);
});

test('cas supervisor cannot create report without required fields', function (): void {
    $cas_user = CasUser::factory()->supervisor()->create([
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('report.store'), []);
    $response->assertSessionHasErrors(['name', 'type']);
});

test('cas supervisor cannot create report without from/to for type 1', function (): void {
    $cas_user = CasUser::factory()->supervisor()->create([
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('report.store'), [
        'name' => 'Test Report',
        'type' => 1,
    ]);
    $response->assertSessionHasErrors(['from', 'to']);
});

/*
|--------------------------------------------------------------------------
| User Form Validation
|--------------------------------------------------------------------------
*/

test('admin cannot create user without required fields', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('administrator.user.store'), []);
    $response->assertSessionHasErrors(['name', 'username', 'type']);
});

test('admin cannot create user with duplicate username', function (): void {
    $user = User::factory()->create(['username' => 'existing_user']);

    $admin = User::factory()->create();
    $response = $this->actingAs($admin)->post(route('administrator.user.store'), [
        'name' => 'Test',
        'username' => 'existing_user',
        'type' => 'admin',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $response->assertSessionHasErrors(['username']);
});

test('admin cannot create cas user without employee_number', function (): void {
    $user = User::factory()->create();
    $role = Role::factory()->create(['name' => 'User']);

    $response = $this->actingAs($user)->post(route('administrator.user.store'), [
        'name' => 'Test CAS',
        'username' => 'test_cas',
        'type' => 'cas',
        'role_id' => $role->id,
    ]);
    $response->assertSessionHasErrors(['employee_number']);
});

test('admin cannot create user with short password', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('administrator.user.store'), [
        'name' => 'Test',
        'username' => 'test_user',
        'type' => 'admin',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);
    $response->assertSessionHasErrors(['password']);
});

test('admin cannot create user with mismatched passwords', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('administrator.user.store'), [
        'name' => 'Test',
        'username' => 'test_user',
        'type' => 'admin',
        'password' => 'password123',
        'password_confirmation' => 'different123',
    ]);
    $response->assertSessionHasErrors(['password_confirmation']);
});

test('admin cannot update user without required fields', function (): void {
    $admin = User::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($admin)->put(route('administrator.user.update', [$user, 'admin']), []);
    $response->assertSessionHasErrors(['name', 'username']);
});

test('admin cannot update cas user without employee_number', function (): void {
    $admin = User::factory()->create();
    $role = Role::factory()->create(['name' => 'User']);
    $cas_user = CasUser::factory()->create([
        'role_id' => $role->id,
        'employee_number' => '123456789',
    ]);

    $response = $this->actingAs($admin)->put(route('administrator.user.update', [$cas_user, 'cas']), [
        'name' => 'Test',
        'username' => 'test_cas',
        'type' => 'cas',
    ]);
    $response->assertSessionHasErrors(['employee_number']);
});

test('admin can create admin user with valid data', function (): void {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin)->post(route('administrator.user.store'), [
        'name' => 'New Admin',
        'username' => 'new_admin',
        'type' => 'admin',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $response->assertRedirect(route('administrator.user.index'));
    $this->assertDatabaseHas('users', ['username' => 'new_admin']);
});

test('admin can create cas user with valid data', function (): void {
    $admin = User::factory()->create();
    $role = Role::factory()->create(['name' => 'User']);

    $response = $this->actingAs($admin)->post(route('administrator.user.store'), [
        'name' => 'New CAS User',
        'username' => 'new_cas',
        'type' => 'cas',
        'employee_number' => '999999',
        'role_id' => $role->id,
    ]);
    $response->assertRedirect(route('administrator.user.index'));
    $this->assertDatabaseHas('cas_users', ['username' => 'new_cas']);
});
