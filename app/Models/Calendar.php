<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active'
    ];

    public function calendarEvents() {
        return $this->hasMany(CalendarEvent::class);
    }
}
