<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CasUser extends Model
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    /**
     * Get the Role that owns the CasUser.
     *
     * @return BelongsTo<Role, CasUser>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
