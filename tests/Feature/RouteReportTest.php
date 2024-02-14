<?php

use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\CasUser;
use App\Models\Report;
use App\Models\ReportData;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('cas supervisors can see all reports', function () {
    $active_reports = Report::factory()->count(3)->create([
        'active' => true,
    ]);
    $inactive_reports = Report::factory()->count(3)->create([
        'active' => false,
    ]);

    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('report.index'))
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->supervisor()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('report.index'));
    $response->assertOk();
    foreach ($active_reports->merge($inactive_reports) as $active_report) {
        $response->assertSee($active_report->name);
    }
});

test('cas users can see active reports', function() {
    $active_reports = Report::factory()->count(3)->create([
        'active' => true,
    ]);
    $inactive_reports = Report::factory()->count(3)->create([
        'active' => false,
    ]);

    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('report.index'))
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('report.index'));
    $response->assertOk();
    foreach ($active_reports as $active_report) {
        $response->assertSee($active_report->name);
    }
    foreach ($inactive_reports as $inactive_report) {
        $response->assertDontSee($inactive_report->name);
    }
});

test('cas user cannot create a new report', function () {
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('report.create'))
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('report.create'));
    $response->assertRedirect(route('report.index'));

    $report = Report::factory()->make([
        'active' => true,
        'from' => '2020-01-01',
        'to' => '2020-01-02',
    ])->toArray();
    $response = $this->post(route('report.store'), $report);
    $response->assertRedirect(route('report.index'));
    $this->assertDatabaseMissing('reports', [
        'name' => $report['name'],
        'type' => $report['type'],
    ]);
});

test('cas supervisor can create a new report', function () {
    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('report.create'))
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->supervisor()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('report.create'));
    $response->assertOk();

    $report = Report::factory()->make([
        'active' => true,
        'from' => '2020-01-01',
        'to' => '2020-01-02',
    ])->toArray();
    $response = $this->post(route('report.store'), $report);
    $response->assertRedirect(route('report.index'));
    $this->assertDatabaseHas('reports', [
        'name' => $report['name'],
        'type' => $report['type'],
        'options' => json_encode([
            'from' => '2020-01-01',
            'to' => '2020-01-02',
        ]),
    ]);
});

test('cas user cannot view inactive reports', function () {
    $report = Report::factory()->create([
        'active' => false
    ]);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('report.show', $report))
        ->assertRedirect(config('cas.cas_client_service') . config('cas.cas_uri'));

    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('report.show', $report));
    $response->assertRedirect(route('report.index'));
});

test('cas supervisor can view inactive reports', function () {
    $report = Report::factory()->create([
        'active' => false
    ]);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('report.show', $report))
        ->assertRedirect(config('cas.cas_client_service') . config('cas.cas_uri'));

    $cas_user = CasUser::factory()->supervisor()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('report.show', $report));
    $response->assertOk();
    $response->assertSee($report->name);
});

test('cas user can view an active report', function () {
    $report = Report::factory()->create([
        'active' => true
    ]);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('report.show', $report))
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('report.show', $report));
    $response->assertOk();
    $response->assertSee($report->name);
});

test('cas supervisor can view an active report', function () {
    $report = Report::factory()->create([
        'active' => true
    ]);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('report.show', $report))
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->supervisor()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('report.show', $report));
    $response->assertOk();
    $response->assertSee($report->name);
});

test('cas user cannot edit a report', function () {
    $report = Report::factory()->create([
        'active' => true
    ]);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('report.edit', $report))
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('report.edit', $report));
    $response->assertRedirect(route('report.index'));
});

test('cas supervisor can edit a report', function () {
    $report = Report::factory()->create([
        'active' => true
    ]);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('report.edit', $report))
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->supervisor()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('report.edit', $report));
    $response->assertOk();
    $response->assertSee($report->name);
});

test('cas user cannot update a report', function () {
    $report = Report::factory()->create([
        'active' => true
    ]);
    $report_data = Report::factory()->make([
        'active' => false,
        'from' => '2020-01-01',
        'to' => '2020-01-02',
    ])->toArray();

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->put(route('report.update', $report), $report_data)
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->put(route('report.update', $report), $report_data);
    $response->assertRedirect(route('report.index'));
    $this->assertDatabaseMissing('reports', [
        'name' => $report_data['name'],
        'type' => $report_data['type'],
        'active' => $report_data['active'],
        'options' => json_encode([
            'from' => $report_data['from'],
            'to' => $report_data['to'],
        ]),
    ]);
});

test('cas supervisor can update a report', function () {
    $report = Report::factory()->create();
    $report_data = Report::factory()->make([
        'from' => '2020-01-01',
        'to' => '2020-01-02',
    ])->toArray();

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->put(route('report.update', $report), $report_data)
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->supervisor()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->put(route('report.update', $report), $report_data);
    $response->assertRedirect(route('report.index'));
    $this->assertDatabaseHas('reports', [
        'name' => $report_data['name'],
        'type' => $report_data['type'],
        'options' => json_encode([
            'from' => $report_data['from'],
            'to' => $report_data['to'],
        ]),
    ]);
});

test('cas user cannot delete a report', function () {
    $report = Report::factory()->create([
        'active' => true
    ]);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->delete(route('report.destroy', $report))
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->delete(route('report.destroy', $report));
    $response->assertRedirect(route('report.index'));
    $this->assertDatabaseHas('reports', $report->toArray());
});

test('cas supervisor can delete a report', function () {
    $report = Report::factory()->create([
        'active' => true
    ]);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->delete(route('report.destroy', $report))
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->supervisor()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->delete(route('report.destroy', $report));
    $response->assertRedirect(route('report.index'));
    $this->assertDatabaseMissing('reports', $report->toArray());
});

test('cas user cannot toggle active status of a report', function () {
    $report = Report::factory()->create([
        'active' => true
    ]);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->post(route('report.toggleActive', $report))
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('report.toggleActive', $report));
    $response->assertOk();
    $report->refresh();
    expect($report->active)->toBeTruthy();
});

test('cas supervisor can toggle active status of a report', function () {
    $report = Report::factory()->create([
        'active' => true
    ]);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->post(route('report.toggleActive', $report))
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->supervisor()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('report.toggleActive', $report));
    $response->assertOk();
    $report->refresh();
    expect($report->active)->toBeFalsy();
});

test('cas user can download a doc with calendar events', function () {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
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
    $response = $this->get(route('report.getCalendarToWord', $report));
    $response->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    cas_login_user($cas_user);

    $response = $this->get(route('report.getCalendarToWord', $report));
    $response->assertOk();
    $this->assertTrue(str_starts_with($response->headers->get('content-disposition'), 'attachment; filename='));
});

test('cas supervisor can download a doc with calendar events', function () {
    $calendar = Calendar::factory()->create();
    $cas_user = CasUser::factory()->supervisor()->create([
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
    $response = $this->get(route('report.getCalendarToWord', $report));
    $response->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    cas_login_user($cas_user);

    $response = $this->get(route('report.getCalendarToWord', $report));
    $response->assertOk();
    $this->assertTrue(str_starts_with($response->headers->get('content-disposition'), 'attachment; filename='));
});

test('cas user cannot upload a file on an inactive report', function () {
    $report = Report::factory()->create(['active' => false]);
    Storage::fake('local');

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->post(route('report.uploadReport', $report), [
        'file' => UploadedFile::fake()->create('report.pdf'),
    ]);
    $response->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('report.uploadReport', $report), [
        'file' => UploadedFile::fake()->create('report.pdf'),
    ]);
    $response->assertRedirect(route('report.index'));
    $report_data = ReportData::where('report_id', $report->id)->first();
    expect($report_data)->toBeNull();
});

test('cas user can upload a file on report', function () {
    $report = Report::factory()->create();
    Storage::fake('local');

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->post(route('report.uploadReport', $report), [
        'file' => UploadedFile::fake()->create('report.pdf'),
    ]);
    $response->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->post(route('report.uploadReport', $report), [
        'file' => UploadedFile::fake()->create('report.pdf'),
    ]);
    $response->assertRedirect(route('report.index'));
    $report_data = ReportData::where('report_id', $report->id)->first();
    expect($report_data)->not->toBeNull();
    $data = json_decode($report_data->data, true);
    Storage::disk('local')->assertExists("reports/{$report->id}/{$cas_user->id}/{$data['filename']}");

    // Ανεβάζοντας άλλο αρχείο όσο η φόρμα είναι ανοιχτή μπορούμε να αλλάξουμε
    // το αρχείο της αναφοράς
    $response = $this->post(route('report.uploadReport', $report), [
        'file' => UploadedFile::fake()->create('report_new.pdf'),
    ]);
    $response->assertRedirect(route('report.index'));
    $report_data = ReportData::where('report_id', $report->id)->first();
    expect($report_data)->not->toBeNull();
    $data = json_decode($report_data->data, true);
    Storage::disk('local')->assertExists("reports/{$report->id}/{$cas_user->id}/{$data['filename']}");
});

test('cas users can download their files of a report', function () {
    $report = Report::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);
    $report_data = ReportData::factory()->create([
        'cas_user_id' => $cas_user->id,
        'report_id' => $report->id,
        'data' => json_encode([
            'filename' => 'test.txt',
            'real_filename' => 'real_test.txt',
        ]),
    ]);

    Storage::shouldReceive('download')
        ->once()
        ->with("reports/{$report->id}/{$report_data->cas_user_id}/test.txt", "real_test.txt")
        ->andReturn(null);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get(route('report.getFile', [$report, $report_data]));
    $response->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    cas_login_user($cas_user);

    $response = $this->get(route('report.getFile', [$report, $report_data]));
    $response->assertOk();
});

test('cas supervisor can download files of a report', function () {
    $report = Report::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);
    $cas_supervisor = CasUser::factory()->supervisor()->create([
        'employee_number' => '1234567890',
    ]);
    $report_data = ReportData::factory()->create([
        'cas_user_id' => $cas_user->id,
        'report_id' => $report->id,
        'data' => json_encode([
            'filename' => 'test.txt',
            'real_filename' => 'real_test.txt',
        ]),
    ]);

    Storage::shouldReceive('download')
        ->once()
        ->with("reports/{$report->id}/{$report_data->cas_user_id}/test.txt", "real_test.txt")
        ->andReturn(null);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get(route('report.getFile', [$report, $report_data]));
    $response->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    cas_login_user($cas_supervisor);

    $response = $this->get(route('report.getFile', [$report, $report_data]));
    $response->assertOk();
});

test('cas users cannot download files of other users of a report', function () {
    $report = Report::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);
    $cas_user2 = CasUser::factory()->user()->create([
        'employee_number' => '1234567890',
    ]);
    $report_data = ReportData::factory()->create([
        'cas_user_id' => $cas_user2->id,
        'report_id' => $report->id,
        'data' => json_encode([
            'filename' => 'test.txt',
            'real_filename' => 'real_test.txt',
        ]),
    ]);

    Storage::shouldReceive('download')
        ->never()
        ->with("reports/{$report->id}/{$report_data->cas_user_id}/test.txt", "real_test.txt")
        ->andReturn(null);

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get(route('report.getFile', [$report, $report_data]));
    $response->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    cas_login_user($cas_user);

    $response = $this->get(route('report.getFile', [$report, $report_data]));
    $response->assertForbidden();
});

test('cas user cannot download all files of a report', function () {
    $report = Report::factory()->create();
    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '123456789',
    ]);

    // Φτιάξε 10 χρήστες και 10 αντίστοιχες απαντήσεις
    $casUsers = CasUser::factory()
        ->count(10)
        ->user()
        ->sequence(fn (Sequence $sequence) => [
            'employee_number' => $sequence->index + 1,
        ])
        ->create();
    $reportData = ReportData::factory()
        ->count(10)
        ->sequence(fn (Sequence $sequence) => [
            'cas_user_id' => $casUsers[$sequence->index]->id,
            'report_id' => $report['id'],
            'data' => json_encode([
                'filename' => "{$casUsers[$sequence->index]->id}.pdf",
                'real_filename' => "test{$casUsers[$sequence->index]->id}.pdf",
            ]),
        ])
        ->create();
    $files = [];
    foreach ($casUsers as $casUser) {
        $filename = "reports/{$report->id}/{$casUser->id}/{$casUser->id}.pdf";
        $files[] = UploadedFile::fake()
            ->create($filename, 1024, 'application/pdf')
            ->storeAs($filename);
    }
    foreach ($files as $file) {
        /** @var Illuminate\Foundation\Testing\TestCase $this */
        $this->assertTrue(Storage::exists($file));
    }

    $response = $this->get(route('report.getAllFiles', $report));
    $response->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    cas_login_user($cas_user);

    $response = $this->get(route('report.getAllFiles', $report));
    $response->assertForbidden();

    foreach ($files as $file) {
        Storage::deleteDirectory("reports/{$report->id}");
        $this->assertFalse(Storage::exists($file));
        $this->assertFalse(Storage::exists("reports/{$report->id}"));
    }
});

test('cas supervisor can download all files of a report', function () {
    $report = Report::factory()->create();
    $cas_user = CasUser::factory()->supervisor()->create([
        'employee_number' => '123456789',
    ]);

    // Φτιάξε 10 χρήστες και 10 αντίστοιχες απαντήσεις
    $casUsers = CasUser::factory()
        ->count(10)
        ->user()
        ->sequence(fn (Sequence $sequence) => [
            'employee_number' => $sequence->index + 1,
        ])
        ->create();
    $reportData = ReportData::factory()
        ->count(10)
        ->sequence(fn (Sequence $sequence) => [
            'cas_user_id' => $casUsers[$sequence->index]->id,
            'report_id' => $report['id'],
            'data' => json_encode([
                'filename' => "{$casUsers[$sequence->index]->id}.pdf",
                'real_filename' => "test{$casUsers[$sequence->index]->id}.pdf",
            ]),
        ])
        ->create();
    $files = [];
    foreach ($casUsers as $casUser) {
        $filename = "reports/{$report->id}/{$casUser->id}/{$casUser->id}.pdf";
        $files[] = UploadedFile::fake()
            ->create($filename, 1024, 'application/pdf')
            ->storeAs($filename);
    }
    foreach ($files as $file) {
        /** @var Illuminate\Foundation\Testing\TestCase $this */
        $this->assertTrue(Storage::exists($file));
    }

    $response = $this->get(route('report.getAllFiles', $report));
    $response->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    cas_login_user($cas_user);

    $response = $this->get(route('report.getAllFiles', $report));
    $response->assertOk();
    $this->assertTrue($response->headers->get('Content-Type') === 'application/zip');

    foreach ($files as $file) {
        Storage::deleteDirectory("reports/{$report->id}");
        $this->assertFalse(Storage::exists($file));
        $this->assertFalse(Storage::exists("reports/{$report->id}"));
    }
});
