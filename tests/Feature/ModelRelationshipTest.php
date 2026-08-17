<?php

use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\CasUser;
use App\Models\Report;
use App\Models\ReportData;

/*
|--------------------------------------------------------------------------
| Calendar Model
|--------------------------------------------------------------------------
*/

test('calendar has many calendar events', function (): void {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create(['employee_number' => '111111']);
    CalendarEvent::factory()->count(3)->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
    ]);

    expect($calendar->events)->toHaveCount(3);
});

test('calendar events relationship returns correct models', function (): void {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create(['employee_number' => '222222']);
    $event = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
    ]);

    expect($calendar->events->first()->id)->toBe($event->id);
});

/*
|--------------------------------------------------------------------------
| CalendarEvent Model
|--------------------------------------------------------------------------
*/

test('calendar event belongs to calendar', function (): void {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create(['employee_number' => '333333']);
    $event = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
    ]);

    expect($event->calendar->id)->toBe($calendar->id);
});

test('calendar event belongs to cas user', function (): void {
    $cas_user = CasUser::factory()->user()->create(['employee_number' => '444444']);
    $calendar = Calendar::factory()->create();
    $event = CalendarEvent::factory()->create([
        'calendar_id' => $calendar->id,
        'cas_user_id' => $cas_user->id,
    ]);

    expect($event->casUser->id)->toBe($cas_user->id);
});

/*
|--------------------------------------------------------------------------
| CasUser Model
|--------------------------------------------------------------------------
*/

test('cas user belongs to role', function (): void {
    $cas_user = CasUser::factory()->supervisor()->create(['employee_number' => '555555']);

    expect($cas_user->role->id)->toBe($cas_user->role_id);
    expect($cas_user->role->name)->toBe('Supervisor');
});

/*
|--------------------------------------------------------------------------
| Report Model
|--------------------------------------------------------------------------
*/

test('report has many report data', function (): void {
    $report = Report::factory()->create();
    $cas_user = CasUser::factory()->user()->create(['employee_number' => '666666']);
    ReportData::factory()->count(2)->create([
        'report_id' => $report->id,
        'cas_user_id' => $cas_user->id,
        'data' => json_encode(['test' => 'data']),
    ]);

    expect($report->data)->toHaveCount(2);
});

test('report type constant is correct', function (): void {
    expect(Report::TypeTrimester)->toBe(1);
});

test('report available types contains expected structure', function (): void {
    expect(Report::AvailableTypes)->toHaveCount(1);
    expect(Report::AvailableTypes[0]['id'])->toBe(1);
    expect(Report::AvailableTypes[0]['name'])->toBe('Τριμήνου');
});

/*
|--------------------------------------------------------------------------
| ReportData Model
|--------------------------------------------------------------------------
*/

test('report data belongs to cas user', function (): void {
    $cas_user = CasUser::factory()->user()->create(['employee_number' => '777777']);
    $report = Report::factory()->create();
    $report_data = ReportData::factory()->create([
        'cas_user_id' => $cas_user->id,
        'report_id' => $report->id,
        'data' => json_encode(['test' => 'data']),
    ]);

    expect($report_data->cas_user->id)->toBe($cas_user->id);
});

test('report data belongs to report', function (): void {
    $cas_user = CasUser::factory()->user()->create(['employee_number' => '888888']);
    $report = Report::factory()->create();
    $report_data = ReportData::factory()->create([
        'cas_user_id' => $cas_user->id,
        'report_id' => $report->id,
        'data' => json_encode(['test' => 'data']),
    ]);

    expect($report_data->report->id)->toBe($report->id);
});
