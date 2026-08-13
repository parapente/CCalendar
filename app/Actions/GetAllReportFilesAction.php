<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Report;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

final class GetAllReportFilesAction
{
    public function __invoke(Request $request, Report $report): BinaryFileResponse
    {
        $cas_user = $request->input('cas_user');
        $cas_user_role = $request->input('cas_user_role');

        if (! $request->user() && $cas_user_role !== 'Supervisor') {
            abort(403);
        }

        $user_path = $cas_user ? "/cas/{$cas_user->id}" : '/user/'.request()->user()->id;
        $now = DateTime::createFromFormat('U.u', (string) microtime(true));

        if ($now === false) {
            throw new \DateException('Cannot create date from microtime');
        }

        $zip = new ZipArchive;
        $zip_path = '/tmp'.$user_path.'/';
        Storage::makeDirectory($zip_path);
        $zip_name = $now->format('YmdHisu').'.zip';
        $zip->open(storage_path('app').$zip_path.$zip_name, ZipArchive::CREATE);

        foreach ($report->data as $line) {
            $decoded_data = json_decode($line->data);
            $file_path = storage_path('app')."/reports/{$report->id}/{$line->cas_user_id}/{$decoded_data->filename}";

            // Αλλαγή ονόματος μέσα στο zip ώστε να μην υπάρχει πιθανότητα να
            // συμπέσουν δύο αρχεία να έχουν το ίδιο όνομα
            $filename = $line->cas_user->name.'.'.(pathinfo($decoded_data->filename)['extension'] ?? '');
            $zip->addFile($file_path, $filename);
            $zip->setCompressionName($filename, ZipArchive::CM_STORE);
        }

        $zip->close();

        return response()->download(storage_path('app').$zip_path.$zip_name);
    }
}
