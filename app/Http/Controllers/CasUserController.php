<?php

namespace App\Http\Controllers;

use App\Models\CasUser;
use Inertia\Inertia;

class CasUserController extends Controller
{
    public function invalidCasUser()
    {
        cas()->authenticate();
        $user = array_filter(cas()->getAttributes(), fn($key): bool => in_array($key, ['cn', 'uid', 'employeenumber', 'mail']), ARRAY_FILTER_USE_KEY);

        return Inertia::render('Auth/InvalidCasUser', ['user' => $user]);
    }

    // public function getName(CasUser $user)
    // {
    //     if (auth()->user() || (cas()->isAuthenticated() && request('cas_user_role') === 'Supervisor')) {
    //         return json_encode([
    //             'id' => $user->id,
    //             'name' => $user->name,
    //         ]);
    //     } else {
    //         abort(403);
    //     }
    // }
}
