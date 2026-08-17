<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Calendar;
use Inertia\Inertia;

final class CalendarOverviewController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $calendars = Calendar::all();

        return Inertia::render('Admin/Calendar/Overview', ['calendars' => $calendars]);
    }
}
