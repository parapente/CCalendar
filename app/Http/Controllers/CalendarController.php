<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalendarEventRequest;
use App\Http\Requests\StoreCalendarRequest;
use App\Http\Requests\UpdateCalendarRequest;
use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\CasUser;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $calendars = Calendar::all();

        return Inertia::render('Admin/Calendar/Index', ['calendars' => $calendars]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Calendar/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCalendarRequest $request)
    {
        Calendar::create($request->validated());

        return redirect()->route('administrator.calendar.index')
            ->with('flash.bannerStyle', 'success')
            ->with('flash.banner', 'Το ημερολόγιο δημιουργήθηκε επιτυχώς');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Calendar $calendar)
    {
        return Inertia::render('Admin/Calendar/Edit', ['calendar' => $calendar]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCalendarRequest $request, Calendar $calendar)
    {
        $calendar->update($request->validated());

        return redirect()->route('administrator.calendar.index')
            ->with('flash.bannerStyle', 'success')
            ->with('flash.banner', "Το ημερολόγιο $calendar->name ενημερώθηκε επιτυχώς");
    }

    public function toggleActive(Calendar $calendar): void
    {
        $calendar->active = ! $calendar->active;
        $calendar->save();
    }

    public function addEvent(Calendar $calendar, StoreCalendarEventRequest $request): string
    {
        if (! $request->id) {
            $calendar->calendarEvents()->create([
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
            $calendarEvent = CalendarEvent::findOrFail($request->id);

            /** @var CasUser $user */
            $user = $request->input('cas_user');
            if (! $user || ($user->id !== $calendarEvent->cas_user_id)) {
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

    public function deleteEvent(Calendar $calendar, CalendarEvent $event, Request $request): string
    {
        /** @var CasUser $user */
        $user = $request->input('cas_user');
        if (! $user || ($user->id !== $event->cas_user_id)) {
            return json_encode(['success' => false, 'message' => 'Δεν επιτρέπεται η λειτουργία σε αυτόν τον χρήστη']);
        }

        $deleted = $event->delete();

        if ($deleted) {
            return json_encode(['success' => true, 'message' => 'Η εκδήλωση διαγράφηκε επιτυχώς!']);
        } else {
            return json_encode(['success' => false, 'message' => 'Η εκδήλωση δεν διαγράφηκε!']);
        }
    }

    public function showAll()
    {
        $calendars = Calendar::where('active', true)->get();

        return Inertia::render('Calendar', ['calendars' => $calendars]);
    }

    public function overview()
    {
        $calendars = Calendar::all();

        return Inertia::render('Admin/Calendar/Overview', ['calendars' => $calendars]);
    }
}
