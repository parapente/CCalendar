<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Http\Request;

interface CasAuthInterface
{
    public function authenticate(Request $request);
    public function getCasUser(): array;
}
