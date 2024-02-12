<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalendarEventRequest;
use App\Http\Requests\UpdateCalendarEventRequest;
use App\Models\CalendarEvent;
use App\Models\Role;

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
            ->whereHas("calendar", function ($query) {
                $query->where('active', true);
            });

        if (!$getAll) {
            $calendarEvents = $calendarEvents->where('cas_user_id', request('cas_user')->id);
        }

        $calendarEvents = $calendarEvents->where(function ($query) use ($year, $month) {
            $query->where(function ($query) use ($year, $month) {
                $query->whereYear('start_date', $year)
                    ->whereMonth('start_date', $month);
            })
            ->orWhere(function ($query) use ($year, $month) {
                $query->whereYear('end_date', $year)
                    ->whereMonth('end_date', $month);
            })
            ->orWhere(function ($query) use ($year, $month) {
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
    public function store(StoreCalendarEventRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarEvent $calendarEvent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CalendarEvent $calendarEvent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCalendarEventRequest $request, CalendarEvent $calendarEvent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarEvent $calendarEvent)
    {
        //
    }
}
