<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\CasUser;
use Illuminate\Http\Request;

final class CalendarEventToggleActiveAction
{
    public function __invoke(Request $request, Calendar $calendar, CalendarEvent $event): string
    {
        /** @var CasUser $user */
        $user = $request->input('cas_user');
        if (! $user || ($user->id !== $event->cas_user_id)) {
            return json_encode(['success' => false, 'message' => 'Δεν επιτρέπεται η λειτουργία σε αυτόν τον χρήστη']);
        }

        $event->cancelled = ! $event->cancelled;

        if ($event->cancelled) {
            $message = 'Η εκδήλωση ακυρώθηκε';
        } else {
            $message = 'Η εκδήλωση σημειώθηκε ως ενεργή';
        }

        if ($event->save()) {
            return json_encode(['success' => true, 'message' => $message]);
        }

        return json_encode(['success' => false, 'message' => 'Απέτυχε η αποθήκευση της τροποποίησης']);
    }
}
