<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Http\Requests\UploadReportRequest;
use App\Models\CalendarEvent;
use App\Models\CasUser;
use App\Models\Report;
use App\Models\ReportData;
use App\Models\Role;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        [$cas_user, $cas_user_role] = \App\Utils\Cas::getCasUser();

        if ($user || ($cas_user && $cas_user_role === 'Supervisor')) { // Οι διαχειριστές τα βλέπουν όλα
            $reports = Report::orderBy('created_at', 'desc')->paginate(15);
        } else {
            $reports = Report::where('active', 1)->orderBy('created_at', 'desc')->paginate(15);
        }

        if ($user) {
            return Inertia::render('Admin/Report/Index', compact('reports'));
        }

        return Inertia::render('Report/Index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (request()->user()) {
            return Inertia::render('Admin/Report/Create', ['types' => Report::AvailableTypes]);
        } else {
            return Inertia::render('Report/Create', ['types' => Report::AvailableTypes]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportRequest $request)
    {
        $report = Report::create([
            'name' => $request->name,
            'type' => $request->type,
            'options' => json_encode([
                'from' => $request->from,
                'to' => $request->to
            ])
        ]);

        if (request()->user()) {
            $route = 'administrator.report.index';
        } else {
            $route = 'report.index';
        }

        if (!$report) {
            return redirect()->route($route)
                ->with('flash.bannerStyle', 'danger')
                ->with('flash.banner', 'Η δημιουργία αναφοράς απέτυχε!');
        }

        return redirect()->route($route)
            ->with('flash.bannerStyle', 'success')
            ->with('flash.banner', 'Η αναφορά δημιουργήθηκε επιτυχώς');
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        [$cas_user, $cas_user_role] = \App\Utils\Cas::getCasUser();

        if ($cas_user && $cas_user_role === 'User') {
            return Inertia::render('Report/Show', [
                'report' => $report
            ]);
        }

        $users_that_answered = $report->data->map(function ($item) {
            return $item->cas_user->id;
        });

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
                    'missing' => $missing
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        return Inertia::render('Admin/Report/Edit', [
            'report' => $report,
            'types' => Report::AvailableTypes
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReportRequest $request, Report $report)
    {
        $report->name = $request->name;
        $report->type = $request->type;
        $report->options = json_encode([
            'from' => $request->from,
            'to' => $request->to
        ]);
        $result = $report->save();

        if (!$result) {
            return redirect()->route('administrator.report.index')
                ->with('flash.bannerStyle', 'danger')
                ->with('flash.banner', 'Η επεξεργασία της αναφοράς απέτυχε!');
        }

        return redirect()->route('administrator.report.index')
            ->with('flash.bannerStyle', 'success')
            ->with('flash.banner', 'Η αναφορά ενημερώθηκε επιτυχώς');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        $result = $report->delete();

        if ($result) {
            return redirect()->route('administrator.report.index')
                ->with('flash.bannerStyle', 'success')
                ->with('flash.banner', 'Η αναφορά διαγράφηκε επιτυχώς');
        }

        return redirect()->route('administrator.report.index')
            ->with('flash.bannerStyle', 'danger')
            ->with('flash.banner', 'Αποτυχία διαγραφής αναφοράς!');
    }

    public function toggleActive(Report $report)
    {
        $report->active = !$report->active;
        $result = $report->save();

        if (!$result) {
            throw new \Exception('Αποτυχία αλλαγής κατάστασης αναφοράς!');
        }
    }

    public function getCalendarToWord(Report $report)
    {
        $options = json_decode($report->options);
        /** @var \Illuminate\Database\Eloquent\Collection */
        $events = CalendarEvent::with('calendar')
            ->where('start_date', '>=', $options->from)
            ->where('end_date', '<', $options->to)
            ->when(request('cas_user'), function ($query) {
                $query->where('cas_user_id', request('cas_user')->id);
            })
            ->orderBy('start_date', 'asc')
            ->get();

        $tableData = $events->map(function ($event, $key) {
            $details = $event->description ? "{$event->description}\n" : '';
            $details .= $event->location ? "Τοποθεσία: {$event->location}\n" : '';
            $details .= $event->url ? "Ιστοσελίδα: {$event->url}\n" : '';

            // Μετέτρεψε τα \n σε αλλαγή γραμμής
            $details = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', $details );

            return [
                'aa' => ++$key,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,
                'type' => $event->calendar->name,
                'title' => $event->title,
                'details' => $details,
            ];
        });

        $templateProcessor = new TemplateProcessor(app()->path().'/WordTemplates/calendarEvents.docx');
        $templateProcessor->setValue('from', $options->from);
        $templateProcessor->setValue('to', $options->to);
        $templateProcessor->cloneRowAndSetValues('aa', $tableData);

        $tmpPart = request()->user() ? request()->user()->id : request('cas_user')->id;
        $tmpPart .= "-" . Carbon::now()->timestamp;
        $filename = "calendarEvents-".$tmpPart.".docx";
        $templateProcessor->saveAs(storage_path() . "/" . $filename);
        $output = file_get_contents(storage_path() . "/" . $filename);
        File::delete(storage_path() . "/" . $filename);

        return response()->streamDownload(function () use ($output) {
            echo $output;
        }, $filename, [
            'Content-Length' => strlen($output),
        ]);
    }

    public function uploadReport(UploadReportRequest $request, Report $report)
    {
        $user_id = request('cas_user')->id;
        $path = "reports/{$report->id}/$user_id";
        if (!Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        $file = $request->file('file');
        $name = $file->hashName();

        Storage::putFileAs(
            $path,
            $request->file('file'),
            "$name"
        );

        $report_data = ReportData::where('cas_user_id', $user_id)
            ->where('report_id', $report->id)
            ->first();

        if ($report_data) {
            // Σβήσε το παλιό αρχείο
            $current_data = json_decode($report_data->data);
            $old_file = $path . "/" . $current_data->filename;
            if (Storage::exists($old_file)) {
                Storage::delete($old_file);
            }

            $report_data->data = json_encode([
                'filename' => "$name",
                'real_filename' => "{$file->getClientOriginalName()}"
            ]);
            $report_data->save();
        } else {
            ReportData::create([
                'cas_user_id' => $user_id,
                'report_id' => $report->id,
                'data' => json_encode([
                    'filename' => "$name",
                    'real_filename' => "{$file->getClientOriginalName()}"
                ])
            ]);
        }

        return redirect()->route('report.index')
            ->with('flash.bannerStyle', 'success')
            ->with('flash.banner', 'Η αναφορά αποθηκεύτηκε επιτυχώς');
    }

    public function getFile(Report $report, ReportData $reportData)
    {
        $data = json_decode($reportData->data);

        return Storage::download("reports/{$report->id}/{$reportData->cas_user_id}/{$data->filename}", $data->real_filename);
    }

    public function getAllFiles(Report $report)
    {
        [$cas_user, $cas_user_role] = \App\Utils\Cas::getCasUser();
        $user_path = $cas_user ? "/cas/{$cas_user->id}" : "/user/" . request()->user()->id;
        $now = DateTime::createFromFormat('U.u', microtime(true));

        $zip = new ZipArchive;
        $zip_path = "/tmp" . $user_path . "/";
        Storage::makeDirectory($zip_path);
        $zip_name = $now->format('YmdHisu') . ".zip";
        $zip->open(storage_path('app') . $zip_path . $zip_name, ZipArchive::CREATE);

        foreach($report->data as $line) {
            $decoded_data = json_decode($line->data);
            $file_path = storage_path('app') . "/reports/{$report->id}/{$line->cas_user_id}/{$decoded_data->filename}";

            // Αλλαγή ονόματος μέσα στο zip ώστε να μην υπάρχει πιθανότητα να
            // συμπέσουν δύο αρχεία να έχουν το ίδιο όνομα
            $filename = $line->cas_user->name . "." . pathinfo($decoded_data->filename)['extension'];
            $zip->addFile($file_path, $filename);
            $zip->setCompressionName($filename, ZipArchive::CM_STORE);
        }

        $zip->close();
        return response()->download(storage_path('app') . $zip_path . $zip_name);
    }
}
