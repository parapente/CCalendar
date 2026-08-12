<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCalendarRequest;
use App\Http\Requests\UpdateCalendarRequest;
use App\Models\Calendar;
use Inertia\Inertia;

final class CalendarController extends Controller
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
}
