<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['cas_user_id', 'report_id', 'data'])]
final class ReportData extends Model
{
    use HasFactory;

    /**
     * Get the CasUser that owns the ReportData.
     *
     * @return BelongsTo<CasUser, $this>
     */
    public function cas_user(): BelongsTo
    {
        return $this->belongsTo(CasUser::class);
    }

    /**
     * Get the Report that owns the ReportData.
     *
     * @return BelongsTo<Report, $this>
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
