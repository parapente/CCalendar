<?php

use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\CasUser;
use App\Models\Report;
use App\Models\ReportData;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('admin can view reports', function () {
    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get(route('administrator.report.index'));
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $report = Report::factory()->create();
    $response = $this->actingAs($user)->get(route('administrator.report.index'));
    $response
        ->assertOk()
        ->assertSee($report->name);
});

test('admin can create a new report', function () {
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get(route('administrator.report.create'));
    $response->assertRedirect(route('login'));
    $response = $this->post(route('administrator.report.store'), [
        'name' => 'New report',
        'type' => 1,
        'from' => '2021-01-01',
        'to' => '2021-01-02',
    ]);
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('administrator.report.create'));
    $response->assertOk();
    $response = $this->actingAs($user)
        ->post(route('administrator.report.store'), [
            'name' => 'New report',
            'type' => 1,
            'from' => '2021-01-01',
            'to' => '2021-01-02',
        ]);
    $response->assertRedirect(route('administrator.report.index'));

    $this->assertDatabaseHas('reports', [
        'name' => 'New report',
        'type' => 1,
        'options' => json_encode([
            'from' => '2021-01-01',
            'to' => '2021-01-02',
        ]),
    ]);
});

test('admin can edit an existing report', function () {
    $report = Report::factory()->create();
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get(route('administrator.report.edit', $report));
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('administrator.report.edit', $report));
    $response->assertOk();
});

test('admin can update an existing report', function () {
    $report = Report::factory()->create();
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->put(route('administrator.report.update', $report), [
        'name' => 'Updated report',
        'type' => 1,
        'from' => '2021-01-01',
        'to' => '2021-01-02',
    ]);
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)
        ->put(route('administrator.report.update', $report), [
            'name' => 'Updated report',
            'type' => 1,
            'from' => '2021-01-01',
            'to' => '2021-01-02',
        ]);
    $response->assertRedirect(route('administrator.report.index'));
    $this->assertDatabaseHas('reports', [
        'name' => 'Updated report',
        'type' => 1,
        'options' => json_encode([
            'from' => '2021-01-01',
            'to' => '2021-01-02',
            ]),
    ]);
});

test('admin can delete an existing report', function () {
    $report = Report::factory()->create();
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->delete(route('administrator.report.destroy', $report));
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)
        ->delete(route('administrator.report.destroy', $report));
    $response->assertRedirect(route('administrator.report.index'));
    $this->assertDatabaseMissing('reports', [
        'name' => 'Updated report',
        'type' => 1,
        'options' => json_encode([
            'from' => '2021-01-01',
            'to' => '2021-01-02',
            ]),
    ]);
});

test('admin can toggle active status of a report', function () {
    $report = Report::factory()->create();
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->post(route('administrator.report.toggleActive', $report));
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)
        ->post(route('administrator.report.toggleActive', $report));
    $response->assertOk();
    $this->assertDatabaseHas('reports', [
        'id' => $report->id,
        'name' => $report->name,
        'type' => 1,
        'active' => 0,
    ]);
});

test('admin can download all files of a report', function () {
    $report = Report::factory()->create();
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get(route('administrator.report.getAllFiles', $report));
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)
        ->get(route('administrator.report.getAllFiles', $report));
    $response->assertOk();
})->skip();

test('admin can download a file of a report', function () {
    $report = Report::factory()->create();
    $role = Role::factory()->create(['name' => 'User']);
    $casUser = CasUser::factory()->create([
        'role_id' => $role->id,
        'employee_number' => '123456789',
    ]);
    $reportData = ReportData::factory()->create([
        'cas_user_id' => $casUser->id,
        'report_id' => $report->id,
        'data' => json_encode([
            'filename' => 'test.txt',
            'real_filename' => 'real_test.txt',
        ]),
    ]);

    Storage::shouldReceive('download')
        ->once()
        ->with("reports/{$report->id}/{$reportData->cas_user_id}/test.txt", "real_test.txt")
        ->andReturn(null);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get(route('administrator.report.getFile', [$report, $reportData]));
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)
        ->get(route('administrator.report.getFile', [$report, $reportData]));
    $response->assertOk();
});

test('admin can upload a file on a report', function () {
})->skip();

test('admin can download a doc with calendar events', function () {
    $calendar = Calendar::factory()->create();
    $role = Role::factory()->create(['name' => 'User']);
    $cas_user = CasUser::factory()->create([
        'role_id' => $role->id,
        'employee_number' => '123456789',
    ]);
    CalendarEvent::factory()->create([
        'title' => 'Event 1',
        'description' => 'Description 1',
        'start_date' => '2021-01-01',
        'end_date' => '2021-01-02',
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
    ]);
    $report = Report::factory()->create([
        'type' => 1,
        'options' => json_encode([
            'from' => '2021-01-01',
            'to' => '2021-01-31',
        ]),
    ]);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get(route('administrator.report.getCalendarToWord', $report));
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)
        ->get(route('administrator.report.getCalendarToWord', $report));
    $response->assertOk();
    $this->assertTrue(str_starts_with($response->headers->get('content-disposition'), 'attachment; filename='));
});
