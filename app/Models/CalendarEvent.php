<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
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
        'cas_user_id'
    ];

    public function calendar() {
        return $this->belongsTo(Calendar::class);
    }
}
