<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalendarEventRequest;
use App\Http\Requests\StoreCalendarRequest;
use App\Http\Requests\UpdateCalendarRequest;
use App\Models\Calendar;
use App\Models\CalendarEvent;
use Inertia\Inertia;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $calendars = Calendar::all();

        return Inertia::render('Admin/Calendar/Index', compact('calendars'));
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
        Calendar::create([
            'name' => $request->name,
            'color' => $request->color,
            'active' => $request->active,
        ]);

        return redirect()->route('administrator.calendar.index')
            ->with('flash.bannerStyle', 'success')
            ->with('flash.banner', 'Το ημερολόγιο δημιουργήθηκε επιτυχώς');
    }

    /**
     * Display the specified resource.
     */
    public function show(Calendar $calendar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Calendar $calendar)
    {
        return Inertia::render('Admin/Calendar/Edit', compact('calendar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCalendarRequest $request, Calendar $calendar)
    {
        $calendar->update([
            'name' => $request->name,
            'color' => $request->color,
            'active' => $request->active,
        ]);

        return redirect()->route('administrator.calendar.index')
            ->with('flash.bannerStyle','success')
            ->with('flash.banner', "Το ημερολόγιο $calendar->name ενημερώθηκε επιτυχώς");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calendar $calendar)
    {
        //
    }

    public function toggleActive(Calendar $calendar)
    {
        $calendar->active = !$calendar->active;
        $calendar->save();
    }

    public function addEvent(Calendar $calendar, StoreCalendarEventRequest $request)
    {
        if (!$request->id) {
            $calendar->calendarEvents()->create([
                'title' => $request->title,
                'description' => $request->description ?? "",
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'calendar_id' => $calendar->id,
                'location' => $request->location ?? "",
                'url' => $request->url ?? "",
                'cas_user_id' => $request->cas_user->id,
            ]);
        } else {
            // Ενημέρωσε παλιά εκδήλωση
            $calendarEvent = CalendarEvent::findOrFail($request->id);

            $calendarEvent->update([
                'title' => $request->title,
                'description' => $request->description ?? "",
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'calendar_id' => $calendar->id,
                'location' => $request->location ?? "",
                'url' => $request->url ?? "",
            ]);
        }

        return json_encode(["success" => true, "message" => "Η εκδήλωση προστέθηκε επιτυχώς!"]);
    }

    public function deleteEvent(Calendar $calendar, CalendarEvent $event)
    {
        $deleted = $event->delete();

        if ($deleted) {
            return json_encode(["success" => true, "message" => "Η εκδήλωση διαγράφηκε επιτυχώς!"]);
        } else {
            return json_encode(["success" => false, "message" => "Η εκδήλωση δεν διαγράφηκε!"]);
        }
    }

    public function showAll()
    {
        $calendars = Calendar::where('active', true)->get();

        return Inertia::render('Calendar', compact('calendars'));
    }

    public function overview()
    {
        $calendars = Calendar::all();

        return Inertia::render('Admin/Calendar/Overview', compact('calendars'));
    }
}
