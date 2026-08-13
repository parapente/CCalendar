<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UploadReportRequest;
use App\Models\Report;
use App\Models\ReportData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

final class ReportFileController extends Controller
{
    public function store(UploadReportRequest $request, Report $report): RedirectResponse
    {
        if (! $report->active) {
            return redirect()->route('report.index')
                ->with('flash.bannerStyle', 'danger')
                ->with('flash.banner', 'Η αναφορά είναι απενεργοποιημένη! Το αρχείο δεν αποθηκεύτηκε');
        }

        $user_id = request('cas_user')->id;
        $path = "reports/{$report->id}/$user_id";
        if (! Storage::exists($path)) {
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
            $old_file = $path.'/'.$current_data->filename;
            if (Storage::exists($old_file)) {
                Storage::delete($old_file);
            }

            $file_data = json_encode([
                'filename' => "$name",
                'real_filename' => "{$file->getClientOriginalName()}",
            ]);

            if ($file_data === false) {
                throw new \Exception("Cannot encode file data. Filename: '$name', Real filename: '{$file->getClientOriginalName()}' ");
            }

            $report_data->data = $file_data;
            $report_data->save();
        } else {
            ReportData::create([
                'cas_user_id' => $user_id,
                'report_id' => $report->id,
                'data' => json_encode([
                    'filename' => "$name",
                    'real_filename' => "{$file->getClientOriginalName()}",
                ]),
            ]);
        }

        return redirect()->route('report.index')
            ->with('flash.bannerStyle', 'success')
            ->with('flash.banner', 'Η αναφορά αποθηκεύτηκε επιτυχώς');
    }
}
