<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Calendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'active',
        'shared'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'shared' => 'boolean',
        ];
    }

    /**
     * Get the CalendarEvents for the Calendar.
     *
     * @return HasMany<CalendarEvent, Calendar>
     */
    public function calendarEvents(): HasMany {
        return $this->hasMany(CalendarEvent::class);
    }
}
