<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CalendarToWordAction;
use App\Models\Report;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class CalendarToWordController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Report $report, CalendarToWordAction $action): StreamedResponse
    {
        return $action->handle($report);
    }
}
