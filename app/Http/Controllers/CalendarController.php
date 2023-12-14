<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalendarEventRequest;
use App\Http\Requests\StoreCalendarRequest;
use App\Http\Requests\UpdateCalendarRequest;
use App\Models\Calendar;
use Inertia\Inertia;

use function Pest\Laravel\json;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $calendars = Calendar::where('active', true)->get();

        return Inertia::render('Calendar', compact('calendars'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
            ]);
        } else {
            // Ενημέρωσε παλιά εκδήλωση
            $calendar->calendarEvents()->where('id', $request->id)->update([
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
}
