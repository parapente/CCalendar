<?php

declare(strict_types=1);

namespace App\Actions;

use Inertia\Inertia;

final class ShowInvalidCasUserAction
{
    public function __invoke()
    {
        cas()->authenticate();
        $user = array_filter(cas()->getAttributes(), fn ($key): bool => in_array($key, ['cn', 'uid', 'employeenumber', 'mail']), ARRAY_FILTER_USE_KEY);

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
