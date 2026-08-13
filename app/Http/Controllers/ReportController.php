<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\CasUser;
use App\Models\Report;
use App\Models\ReportData;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

final class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = request()->user();
        $cas_user = $request->input('cas_user');
        $cas_user_role = $request->input('cas_user_role');

        if ($user || ($cas_user && $cas_user_role === 'Supervisor')) { // Οι διαχειριστές τα βλέπουν όλα
            $reports = Report::with('data')
                ->orderBy('created_at', 'desc')
                ->paginate();

            $answered = $reports->filter(fn ($report): int => count($report->data))
                ->map(fn ($report) => $report->id)
                ->toArray();
            $answered = array_values($answered);
        } else {
            $reports = Report::with('data')
                ->where('active', 1)
                ->orderBy('created_at', 'desc')
                ->paginate();

            $answered = $reports->filter(fn ($report) => $report->data->where('cas_user_id', $cas_user->id)->count())
                ->map(fn ($report) => $report->id)
                ->toArray();
            $answered = array_values($answered);

            // Αφαίρεσε τα δεδομένα για ασφάλεια
            foreach ($reports as $report) {
                $report->unsetRelation('data');
            }
        }

        if ($user) {
            return Inertia::render('Admin/Report/Index', ['reports' => $reports, 'answered' => $answered]);
        }

        return Inertia::render('Report/Index', ['reports' => $reports, 'answered' => $answered]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (request()->user()) {
            return Inertia::render('Admin/Report/Create', ['types' => Report::AvailableTypes]);
        } else {
            $cas_user_role = $request->input('cas_user_role');

            if ($cas_user_role === 'Supervisor') {
                return Inertia::render('Report/Create', ['types' => Report::AvailableTypes]);
            } else {
                return to_route('report.index');
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportRequest $request)
    {
        if (! $request->user() && $request->input('cas_user_role') !== 'Supervisor') {
            return redirect()->route('report.index')
                ->with('flash.bannerStyle', 'danger')
                ->with('flash.banner', 'Δεν έχετε δικαίωμα δημιουργίας αναφοράς!');
        }

        $report = Report::create([
            'name' => $request->name,
            'type' => $request->type,
            'options' => json_encode([
                'from' => $request->from,
                'to' => $request->to,
            ]),
        ]);

        if (request()->user()) {
            $route = 'administrator.report.index';
        } else {
            $route = 'report.index';
        }

        // if (! $report) {
        //     return redirect()->route($route)
        //         ->with('flash.bannerStyle', 'danger')
        //         ->with('flash.banner', 'Η δημιουργία αναφοράς απέτυχε!');
        // }

        return redirect()->route($route)
            ->with('flash.bannerStyle', 'success')
            ->with('flash.banner', 'Η αναφορά δημιουργήθηκε επιτυχώς');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Report $report)
    {
        $cas_user = $request->input('cas_user');
        $cas_user_role = $request->input('cas_user_role');

        if ($cas_user && $cas_user_role === 'User') {
            if (! $report->active) {
                return redirect()->route('report.index')
                    ->with('flash.bannerStyle', 'danger')
                    ->with('flash.banner', 'Δεν είναι δυνατή η προβολή ανενεργής αναφοράς!');
            }

            return Inertia::render('Report/Show', [
                'report' => $report,
                'data' => ReportData::where('cas_user_id', $cas_user->id)
                    ->where('report_id', $report->id)
                    ->get(),
            ]);
        }

        $users_that_answered = $report->data->map(fn (ReportData $item) => $item->cas_user->id);

        $missing = CasUser::where('active', 1)
            ->where('role_id', '!=', Role::where('name', 'Supervisor')->first()->id)
            ->whereNotIn('id', $users_that_answered)
            ->get();

        if (request()->user() || ($cas_user && $cas_user_role === 'Supervisor')) {
            return Inertia::render(
                request()->user() ? 'Admin/Report/Show' : 'Report/Show',
                [
                    'report' => $report,
                    'data' => $report->data,
                    'missing' => $missing,
                ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Report $report)
    {
        $cas_user_role = $request->input('cas_user_role');

        if (! $request->user() && $cas_user_role !== 'Supervisor') {
            return to_route('report.index');
        }

        if ($cas_user_role === 'Supervisor') {
            return Inertia::render('Report/Edit', [
                'report' => $report,
                'types' => Report::AvailableTypes,
            ]);
        }

        return Inertia::render('Admin/Report/Edit', [
            'report' => $report,
            'types' => Report::AvailableTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReportRequest $request, Report $report)
    {
        if (! $request->user() && $request->input('cas_user_role') !== 'Supervisor') {
            return redirect()->route('report.index')
                ->with('flash.bannerStyle', 'danger')
                ->with('flash.banner', 'Δεν έχετε δικαίωμα ενημέρωσης αναφοράς!');
        }

        $report->name = $request->name;
        $report->type = $request->type;
        $report->options = json_encode([
            'from' => $request->from,
            'to' => $request->to,
        ]);
        $result = $report->save();

        if (request()->user()) {
            $route = 'administrator.report.index';
        } else {
            $route = 'report.index';
        }

        if (! $result) {
            return redirect()->route($route)
                ->with('flash.bannerStyle', 'danger')
                ->with('flash.banner', 'Η επεξεργασία της αναφοράς απέτυχε!');
        }

        return redirect()->route($route)
            ->with('flash.bannerStyle', 'success')
            ->with('flash.banner', 'Η αναφορά ενημερώθηκε επιτυχώς');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Report $report)
    {
        if (! $request->user() && $request->input('cas_user_role') !== 'Supervisor') {
            return redirect()->route('report.index')
                ->with('flash.bannerStyle', 'danger')
                ->with('flash.banner', 'Δεν έχετε δικαίωμα ενημέρωσης αναφοράς!');
        }

        $result = $report->delete();

        if (request()->user()) {
            $route = 'administrator.report.index';
        } else {
            $route = 'report.index';
        }

        if ($result) {
            return redirect()->route($route)
                ->with('flash.bannerStyle', 'success')
                ->with('flash.banner', 'Η αναφορά διαγράφηκε επιτυχώς');
        }

        return redirect()->route($route)
            ->with('flash.bannerStyle', 'danger')
            ->with('flash.banner', 'Αποτυχία διαγραφής αναφοράς!');
    }
}
