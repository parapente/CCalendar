<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\CalendarEvent;
use App\Models\Report;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpWord\Element\PreserveText;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class CalendarToWordAction
{
    public function __invoke(Report $report): StreamedResponse
    {
        $options = json_decode($report->options);
        /** @var Collection<int, CalendarEvent> */
        $events = CalendarEvent::with('calendar')
            ->where('start_date', '>=', $options->from)
            ->where('end_date', '<', $options->to)
            ->when(request('cas_user'), function ($query): void {
                $query->where('cas_user_id', request('cas_user')->id);
            })
            ->orderBy('start_date', 'asc')
            ->get();

        $tableData = $events->map(function (CalendarEvent $event, $key): array {
            $details = $event->description ? "{$event->description}\n" : '';
            $details .= $event->location ? "Τοποθεσία: {$event->location}\n" : '';
            $details .= $event->url ? "Ιστοσελίδα: {$event->url}\n" : '';

            // Μετέτρεψε τα \n σε αλλαγή γραμμής
            // $details = str_replace("\n", '</w:t><w:br/><w:t xml:space="preserve">', $details );
            $details = str_replace("\n", '${newline}', $details);

            return [
                'aa' => ++$key,
                'start_date' => new DateTime($event->start_date)->format('d/m/Y H:i'),
                'end_date' => new DateTime($event->end_date)->format('d/m/Y H:i'),
                'type' => $event->calendar->name,
                'title' => $event->title.($event->cancelled ? ' (Ακυρώθηκε)' : ''),
                'details' => $details,
            ];
        });

        Settings::setOutputEscapingEnabled(true);
        $templateProcessor = new TemplateProcessor(app()->path().'/WordTemplates/calendarEvents.docx');
        $templateProcessor->setValue('from', new DateTime($options->from)->format('d/m/Y'));
        $templateProcessor->setValue('to', new DateTime($options->to)->format('d/m/Y'));
        $templateProcessor->cloneRowAndSetValues('aa', $tableData);

        Settings::setOutputEscapingEnabled(false);
        $newline = new PreserveText('</w:t><w:br/><w:t>');
        while (isset($templateProcessor->getVariableCount()['newline'])) {
            $templateProcessor->setComplexValue('newline', $newline);
        }

        $tmpPart = request()->user() ? request()->user()->id : request('cas_user')->id;
        $tmpPart .= '-'.Carbon::now()->timestamp;
        $filename = 'calendarEvents-'.$tmpPart.'.docx';
        $templateProcessor->saveAs(storage_path().'/'.$filename);
        $output = file_get_contents(storage_path().'/'.$filename);
        File::delete(storage_path().'/'.$filename);

        return response()->streamDownload(function () use ($output): void {
            echo $output;
        }, $filename, [
            'Content-Length' => strlen($output),
        ]);
    }
}
