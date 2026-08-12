<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Report;
use Illuminate\Http\Request;

final class ReportToggleActiveAction
{
    public function __invoke(Request $request, Report $report): void
    {
        if (! $request->user() && $request->input('cas_user_role') !== 'Supervisor') {
            return;
        }

        $report->active = ! $report->active;
        $result = $report->save();

        if (! $result) {
            throw new \Exception('Αποτυχία αλλαγής κατάστασης αναφοράς!');
        }
    }
}
