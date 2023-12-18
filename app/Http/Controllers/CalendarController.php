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
        $calendars = Calendar::where('active', true)->get();

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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCalendarRequest $request, Calendar $calendar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calendar $calendar)
    {
        //
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
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'calendar_id' => $calendar->id,
                'location' => $request->location,
                'url' => $request->url,
            ]);
        }

        return json_encode(["success" => true, "message" => "Event added successfully"]);
    }

    public function deleteEvent(Calendar $calendar, CalendarEvent $event)
    {
        $deleted = $event->delete();

        if ($deleted) {
            return json_encode(["success" => true, "message" => "Event deleted successfully"]);
        } else {
            return json_encode(["success" => false, "message" => "Event not deleted"]);
        }
    }

    public function showAll()
    {
        $calendars = Calendar::where('active', true)->get();

        return Inertia::render('Calendar', compact('calendars'));
    }
}
