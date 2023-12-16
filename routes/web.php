<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
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
});

Route::middleware([
    'cas.auth'
])->group(function () {
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendar/{calendar}/event', [CalendarController::class, 'addEvent'])->name('calendar.addEvent');
    Route::delete('/calendar/{calendar}/event/{event}', [CalendarController::class, 'deleteEvent'])->name('calendar.deleteEvent');
    Route::get('/events/{year}/{month}', [CalendarEventController::class, 'index'])->name('events');
});
