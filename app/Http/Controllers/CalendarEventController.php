<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalendarEventRequest;
use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\CasUser;
use App\Models\Role;
use Illuminate\Http\Request;

final class CalendarEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int $year, int $month): string
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
            ->whereHas('calendar', function ($query): void {
                $query->where('active', true);
            });

        if (! $getAll) {
            $shared_calendars = Calendar::where('shared', true)
                ->get()
                ->map(fn ($item) => $item->id);

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
                    $nextMonth = $month < 12 ? $month + 1 : 1;
                    $nextYear = $nextMonth === 1 ? $year + 1 : $year;
                    $query->whereDate('start_date', '<', "$year-$month-1")
                        ->whereDate('end_date', '>=', "$nextYear-$nextMonth-1");
                });
        })
            ->orderBy('start_date')
            ->get();

        return $calendarEvents->toJson();
    }

    public function store(Calendar $calendar, StoreCalendarEventRequest $request): string|false
    {
        if (! $request->id) {
            $calendar->events()->create([
                'title' => $request->title,
                'description' => $request->description ?? '',
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'calendar_id' => $calendar->id,
                'location' => $request->location ?? '',
                'url' => $request->url ?? '',
                'cas_user_id' => $request->cas_user->id,
                'cancelled' => $request->cancelled ?? false,
            ]);
        } else {
            // Ενημέρωσε παλιά εκδήλωση
            /** @var CalendarEvent $calendarEvent */
            $calendarEvent = CalendarEvent::findOrFail($request->id);

            /** @var CasUser|null $user */
            $user = $request->input('cas_user');
            if ((! $user) || ($user->id !== $calendarEvent->cas_user_id)) {
                return json_encode(['success' => false, 'message' => 'Δεν επιτρέπεται η λειτουργία σε αυτόν τον χρήστη']);
            }

            $calendarEvent->update([
                'title' => $request->title,
                'description' => $request->description ?? '',
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'calendar_id' => $calendar->id,
                'location' => $request->location ?? '',
                'url' => $request->url ?? '',
                'cancelled' => $request->cancelled ? 1 : 0,
            ]);
        }

        return json_encode(['success' => true, 'message' => 'Η εκδήλωση προστέθηκε επιτυχώς!']);
    }

    public function destroy(Calendar $calendar, CalendarEvent $event, Request $request): string|false
    {
        /** @var CasUser|null $user */
        $user = $request->input('cas_user');
        if ((! $user) || ($user->id !== $event->cas_user_id)) {
            return json_encode(['success' => false, 'message' => 'Δεν επιτρέπεται η λειτουργία σε αυτόν τον χρήστη']);
        }

        $deleted = $event->delete();

        if ($deleted) {
            return json_encode(['success' => true, 'message' => 'Η εκδήλωση διαγράφηκε επιτυχώς!']);
        } else {
            return json_encode(['success' => false, 'message' => 'Η εκδήλωση δεν διαγράφηκε!']);
        }
    }
}
