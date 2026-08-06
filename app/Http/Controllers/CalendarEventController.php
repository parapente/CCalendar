<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\Role;
use Illuminate\Http\Request;

class CalendarEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $year, int $month)
    {
        $cas_user = request('cas_user');
        $getAll = false;
        if ($cas_user) {
            $role = Role::where('id', $cas_user->role_id)->firstOrFail()->name;
            if ($role === 'Supervisor') {
                $getAll = true;
            } else {
                $getAll = false;
            }
        } else { // Administrator
            $getAll = true;
        }

        $calendarEvents = CalendarEvent::with('casUser')
            ->whereHas("calendar", function ($query): void {
                $query->where('active', true);
            });

        if (!$getAll) {
            $shared_calendars = Calendar::where('shared', true)
                ->get()
                ->map(fn($item) => $item->id);

            $calendarEvents = $calendarEvents->where(function ($query) use ($shared_calendars): void {
                $query->where('cas_user_id', request('cas_user')->id)
                    ->orWhereIn('calendar_id', $shared_calendars);
            });
        }

        $calendarEvents = $calendarEvents->where(function ($query) use ($year, $month): void {
            $query->where(function ($query) use ($year, $month): void {
                $query->whereYear('start_date', $year)
                    ->whereMonth('start_date', $month);
            })
            ->orWhere(function ($query) use ($year, $month): void {
                $query->whereYear('end_date', $year)
                    ->whereMonth('end_date', $month);
            })
            ->orWhere(function ($query) use ($year, $month): void {
                $nextMonth = $month < 12 ? $month + 1 : 1 ;
                $nextYear = $nextMonth === 1 ? $year + 1 : $year;
                $query->whereDate('start_date', '<', "$year-$month-1")
                    ->whereDate('end_date', '>=', "$nextYear-$nextMonth-1");
            });
        })
        ->orderBy('start_date')
        ->get();

        return $calendarEvents->toJson();
    }

    public function toggleActive(Request $request, Calendar $calendar, CalendarEvent $event)
    {
        /** @var \App\Models\CasUser $user */
        $user = $request->input('cas_user');
        if (!$user || ($user->id !== $event->cas_user_id)) {
            return json_encode(["success" => false, "message" => "Δεν επιτρέπεται η λειτουργία σε αυτόν τον χρήστη"]);
        }

        $event->cancelled =!$event->cancelled;

        if ($event->cancelled) {
            $message = "Η εκδήλωση ακυρώθηκε";
        } else {
            $message = "Η εκδήλωση σημειώθηκε ως ενεργή";
        }

        if ($event->save()) {
            return json_encode(["success" => true, "message" => $message]);
        }

        return json_encode(["success" => false, "message" => "Απέτυχε η αποθήκευση της τροποποίησης"]);
    }
}
