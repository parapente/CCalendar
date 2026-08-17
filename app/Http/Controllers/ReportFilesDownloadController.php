<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\GetAllReportFilesAction;
use App\Models\Report;
use Illuminate\Http\Request;

final class ReportFilesDownloadController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Report $report, GetAllReportFilesAction $action)
    {
        $cas_user = $request->input('cas_user');
        $cas_user_role = $request->input('cas_user_role');

        if (! $request->user() && $cas_user_role !== 'Supervisor') {
            abort(403);
        }

        return $action->handle($report, $cas_user);
    }
}
