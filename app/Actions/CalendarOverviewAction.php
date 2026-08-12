<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Calendar;
use Inertia\Inertia;
use Inertia\Response;

final class CalendarOverviewAction
{
    public function __invoke(): Response
    {
        $calendars = Calendar::all();

        return Inertia::render('Admin/Calendar/Overview', ['calendars' => $calendars]);
    }
}
