<?php

namespace App\Services;

use App\Contracts\CasAuthInterface;
use App\Models\CasUser;

class ProductionCasAuthService implements CasAuthInterface
{
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
