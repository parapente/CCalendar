<?php

namespace App\Services;

use App\Contracts\CasAuthInterface;

class TestCasAuthService implements CasAuthInterface
{
    public function getCasUser(): array
    {
        // Return test user data instead of interacting with phpCAS
        return [null, null];
    }
}
