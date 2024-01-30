<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    public const TypeTrimester = 1;

    public const AvailableTypes = [
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

    public function data(): HasMany
    {
        return $this->hasMany(ReportData::class);
    }
}
