<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CasUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'role_id',
        'employee_number',
        'active',
    ];

    /**
     * Get the Role that owns the CasUser.
     * @return BelongsTo<Role, CasUser>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
