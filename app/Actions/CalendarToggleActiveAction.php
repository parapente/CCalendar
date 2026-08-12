<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Calendar;

final class CalendarToggleActiveAction
{
    public function __invoke(Calendar $calendar): void
    {
        $calendar->active = !$calendar->active;

        $calendar->save();
    }
}
