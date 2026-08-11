<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportData extends Model
{
    use HasFactory;

    protected $fillable = [
        'cas_user_id',
        'report_id',
        'data'
    ];

    /**
     * Get the CasUser that owns the ReportData.
     * @return BelongsTo<CasUser, ReportData>
     */
    public function cas_user(): BelongsTo
    {
        return $this->belongsTo(CasUser::class);
    }

    /**
     * Get the Report that owns the ReportData.
     * @return BelongsTo<Report, ReportData>
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
