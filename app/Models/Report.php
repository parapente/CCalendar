<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Report extends Model
{
    use HasFactory;

    public const int TypeTrimester = 1;

    public const array AvailableTypes = [
        [
            'id' => 1,
            'name' => 'Τριμήνου',
        ],
    ];

    protected $fillable = [
        'name',
        'type',
        'options'
    ];

    /**
     * Get the ReportData for the Report.
     * @return HasMany<ReportData, Report>
     */
    public function data(): HasMany
    {
        return $this->hasMany(ReportData::class);
    }
}
