<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\CasUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

interface CasAuthInterface
{
    public function authenticate(Request $request): RedirectResponse|Response|null;

    /**
     * Get the authenticated CAS user and their role.
     *
     * @return array{CasUser|null,string|null} An array containing the CasUser instance and their role name, or [null, null] if not found.
     */
    public function getCasUser(): array;
}
