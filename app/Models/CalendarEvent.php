<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'url',
        'calendar_id',
        'cas_user_id',
        'cancelled',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cancelled' => 'boolean',
        ];
    }

    /**
     * Get the Calendar that owns the CalendarEvent.
     *
     * @return BelongsTo<Calendar, CalendarEvent>
     */
    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }

    /**
     * Get the CasUser that owns the CalendarEvent.
     *
     * @return BelongsTo<CasUser, CalendarEvent>
     */
    public function casUser(): BelongsTo
    {
        return $this->belongsTo(CasUser::class);
    }
}
