<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CalendarEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'description', 'start_date', 'end_date', 'location', 'url', 'calendar_id', 'cas_user_id', 'cancelled'])]
final class CalendarEvent extends Model
{
    /** @use HasFactory<CalendarEventFactory> */
    use HasFactory;

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
     * @return BelongsTo<Calendar, $this>
     */
    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }

    /**
     * Get the CasUser that owns the CalendarEvent.
     *
     * @return BelongsTo<CasUser, $this>
     */
    public function casUser(): BelongsTo
    {
        return $this->belongsTo(CasUser::class);
    }
}
