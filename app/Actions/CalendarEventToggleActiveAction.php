<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\CalendarEvent;

final class CalendarEventToggleActiveAction
{
    public function handle(CalendarEvent $event): string|false
    {
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
