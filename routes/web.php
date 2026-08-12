<?php

declare(strict_types=1);

use App\Actions\CalendarEventToggleActiveAction;
use App\Actions\CalendarOverviewAction;
use App\Actions\CalendarToggleActiveAction;
use App\Actions\CalendarToWordAction;
use App\Actions\GetAllReportFilesAction;
use App\Actions\GetReportFileAction;
use App\Actions\ReportToggleActiveAction;
use App\Actions\ShowInvalidCasUserAction;
use App\Actions\UserToggleActiveAction;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportFileController;
use App\Http\Controllers\UserCalendarController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', fn () => Inertia::render('Welcome'));

Route::prefix('administrator')
    ->name('administrator.')
    ->middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function (): void {
        Route::get('/', fn () => Inertia::render('Admin/Dashboard'))->name('index');
        Route::get('/user/{user}/type/{type}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/user/{user}/type/{type}', [UserController::class, 'update'])->name('user.update');
        Route::resource('user', UserController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::post('/user/{user}/type/{type}/toggleActive', UserToggleActiveAction::class)->name('user.toggleActive');
        Route::get('/calendar/overview', CalendarOverviewAction::class)->name('calendar.overview');
        Route::post('/calendar/{calendar}/toggleActive', CalendarToggleActiveAction::class)->name('calendar.toggleActive');
        Route::resource('calendar', CalendarController::class)->except(['show', 'destroy']);
        Route::get('/events/{year}/{month}', [CalendarEventController::class, 'index'])->name('events');
        // Route::get('/cas_user/{user}/name', [CasUserController::class, 'getName'])->name('cas_user.name');
        Route::post('/report/{report}/toggleActive', ReportToggleActiveAction::class)->name('report.toggleActive');
        Route::get('/report/{report}/getCalendarToWord', CalendarToWordAction::class)->name('report.getCalendarToWord');
        Route::post('/report/{report}/upload', [ReportFileController::class, 'store'])->name('report.uploadReport');
        Route::get('/report/{report}/getFile/{report_data}', GetReportFileAction::class)->name('report.getFile');
        Route::get('/report/{report}/getAllFiles', GetAllReportFilesAction::class)->name('report.getAllFiles');
        Route::resource('report', ReportController::class);
    });

Route::middleware([
    'cas.registered',
])->group(function (): void {
    Route::get('/calendar', [UserCalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendar/{calendar}/event', [CalendarEventController::class, 'store'])->name('calendarEvent.store');
    Route::delete('/calendar/{calendar}/event/{event}', [CalendarEventController::class, 'destroy'])->name('calendarEvent.destroy');
    Route::post('/calendar/{calendar}/event/{event}/toggleActive', CalendarEventToggleActiveAction::class)->name('calendarEvent.toggleActive');
    Route::get('/events/{year}/{month}', [CalendarEventController::class, 'index'])->name('events');
    // Route::get('/cas_user/{user}/name', [CasUserController::class, 'getName'])->name('cas_user.name');
    Route::post('/report/{report}/toggleActive', ReportToggleActiveAction::class)->name('report.toggleActive');
    Route::get('/report/{report}/getCalendarToWord', CalendarToWordAction::class)->name('report.getCalendarToWord');
    Route::post('/report/{report}/upload', [ReportFileController::class, 'store'])->name('report.uploadReport');
    Route::get('/report/{report}/getFile/{report_data}', GetReportFileAction::class)->name('report.getFile');
    Route::get('/report/{report}/getAllFiles', GetAllReportFilesAction::class)->name('report.getAllFiles');
    Route::resource('report', ReportController::class);
});

Route::get('/invalid/cas_user', ShowInvalidCasUserAction::class)->name('invalid.cas_user');
Route::get('/logout', function (): void {
    cas()->logoutWithUrl(route('calendar.index'));
})->name('cas.logout')->middleware('cas.auth');
