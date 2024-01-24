<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportData extends Model
{
    use HasFactory;

    protected $fillable = [
        'cas_user_id',
        'report_id',
        'data'
    ];

    public function cas_user()
    {
        return $this->belongsTo(CasUser::class);
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
