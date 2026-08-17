<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CalendarEventToggleActiveAction;
use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\CasUser;
use Illuminate\Http\Request;

final class CalendarEventToggleActiveController extends Controller
{
    public function __invoke(Request $request, Calendar $calendar, CalendarEvent $event, CalendarEventToggleActiveAction $action): string|false
    {
        /** @var CasUser|null $user */
        $user = $request->input('cas_user');
        if ((! $user) || ($user->id !== $event->cas_user_id)) {
            return json_encode(['success' => false, 'message' => 'Δεν επιτρέπεται η λειτουργία σε αυτόν τον χρήστη']);
        }

        return $action->handle($event);
    }
}
