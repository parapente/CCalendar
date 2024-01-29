<?php

namespace App\Utils;

use App\Models\CasUser;

class Cas
{
    public static function getCasUser()
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
