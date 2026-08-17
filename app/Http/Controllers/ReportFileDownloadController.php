<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReportFileDownloadController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Report $report, ReportData $reportData): StreamedResponse
    {
        $cas_user = $request->input('cas_user');
        $cas_user_role = $request->input('cas_user_role');

        if (! $request->user() && $cas_user_role !== 'Supervisor' && $cas_user->id !== $reportData->cas_user_id) {
            abort(403);
        }

        $data = json_decode($reportData->data);

        return Storage::download("reports/{$report->id}/{$reportData->cas_user_id}/{$data->filename}", $data->real_filename);
    }
}
