<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\CasUserController;
use App\Http\Controllers\ReportController;
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

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::prefix('administrator')
    ->name('administrator.')
    ->middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('index');
    Route::get('/user/{user}/type/{type}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/{user}/type/{type}', [UserController::class, 'update'])->name('user.update');
    Route::resource('user', UserController::class)->only(['index', 'create', 'store']);
    Route::get('/calendar/overview', [CalendarController::class, 'overview'])->name('calendar.overview');
    Route::post('/calendar/{calendar}/toggleActive', [CalendarController::class, 'toggleActive'])->name('calendar.toggleActive');
    Route::resource('calendar', CalendarController::class)->except(['show', 'destroy']);
    Route::get('/events/{year}/{month}', [CalendarEventController::class, 'index'])->name('events');
    // Route::get('/cas_user/{user}/name', [CasUserController::class, 'getName'])->name('cas_user.name');
    Route::post('/report/{report}/toggleActive', [ReportController::class, 'toggleActive'])->name('report.toggleActive');
    Route::get('/report/{report}/getCalendarToWord', [ReportController::class, 'getCalendarToWord'])->name('report.getCalendarToWord');
    Route::post('/report/{report}/upload', [ReportController::class, 'uploadReport'])->name('report.uploadReport');
    Route::get('/report/{report}/getFile/{report_data}', [ReportController::class, 'getFile'])->name('report.getFile');
    Route::get('/report/{report}/getAllFiles', [ReportController::class, 'getAllFiles'])->name('report.getAllFiles');
    Route::resource('report', ReportController::class);
});

Route::middleware([
    'cas.registered'
])->group(function () {
    Route::get('/calendar', [CalendarController::class, 'showAll'])->name('calendar.index');
    Route::post('/calendar/{calendar}/event', [CalendarController::class, 'addEvent'])->name('calendar.addEvent');
    Route::delete('/calendar/{calendar}/event/{event}', [CalendarController::class, 'deleteEvent'])->name('calendar.deleteEvent');
    Route::get('/events/{year}/{month}', [CalendarEventController::class, 'index'])->name('events');
    // Route::get('/cas_user/{user}/name', [CasUserController::class, 'getName'])->name('cas_user.name');
    Route::post('/report/{report}/toggleActive', [ReportController::class, 'toggleActive'])->name('report.toggleActive');
    Route::get('/report/{report}/getCalendarToWord', [ReportController::class, 'getCalendarToWord'])->name('report.getCalendarToWord');
    Route::post('/report/{report}/upload', [ReportController::class, 'uploadReport'])->name('report.uploadReport');
    Route::get('/report/{report}/getFile/{report_data}', [ReportController::class, 'getFile'])->name('report.getFile');
    Route::get('/report/{report}/getAllFiles', [ReportController::class, 'getAllFiles'])->name('report.getAllFiles');
    Route::resource('report', ReportController::class);
});

Route::get('/invalid/cas_user', [CasUserController::class, 'invalidCasUser'])->name('invalid.cas_user');
Route::get('/logout', function() {
    cas()->logoutWithUrl(route('calendar.index'));
})->name('cas.logout')->middleware('cas.auth');
