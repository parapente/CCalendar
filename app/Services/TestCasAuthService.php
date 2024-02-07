<?php

namespace App\Services;

use App\Contracts\CasAuthInterface;
use Illuminate\Http\Request;

class TestCasAuthService implements CasAuthInterface
{
    public function authenticate(Request $request)
    {
        return redirect(config('cas.cas_client_service') . config('cas.cas_uri'));
    }

    public function getCasUser(): array
    {
        // Return test user data instead of interacting with phpCAS
        return [null, null];
    }
}
