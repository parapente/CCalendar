<?php

namespace App\Services;

use App\Contracts\CasAuthInterface;
use App\Models\CasUser;
use Illuminate\Http\Request;

class ProductionCasAuthService implements CasAuthInterface
{
    protected $cas;

    public function __construct()
    {
        $this->cas = app('cas');
    }

    public function authenticate(Request $request)
    {
        if ($this->cas->checkAuthentication()) {
            // Store the user credentials in a Laravel managed session
            session()->put('cas_user', $this->cas->user());
        } else {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }
            $this->cas->authenticate();
        }
    }

    public function getCasUser(): array
    {
        $cas_user = null;
        $cas_user_role = null;

        if (cas()->isAuthenticated()) {
            $cas_user = CasUser::where('employee_number', cas()->getAttribute('employeenumber'))
                ->orWhere('username', cas()->getAttribute('uid'))
                ->first();
            $cas_user_role = $cas_user?->role->name;
        }

        return [$cas_user, $cas_user_role];
    }
}
