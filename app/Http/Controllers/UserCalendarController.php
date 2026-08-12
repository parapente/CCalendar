<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Calendar;
use Inertia\Inertia;

final class UserCalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $calendars = Calendar::where('active', true)->get();

        return Inertia::render('Calendar', ['calendars' => $calendars]);
    }
}
