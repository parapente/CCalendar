<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Calendar;

final class CalendarToggleActiveController extends Controller
{
    public function __invoke(Calendar $calendar): void
    {
        $calendar->active = ! $calendar->active;

        $calendar->save();
    }
}
