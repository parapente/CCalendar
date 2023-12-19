<?php

namespace App\Http\Middleware;

use App\Models\CasUser;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $cas_user = null;
        $cas_user_role = null;

        if (cas()->isAuthenticated()) {
            $cas_user = CasUser::where('employee_number', cas()->getAttribute('employeenumber'))->first();
            $cas_user_role = Role::where('cas_user_id', $cas_user->id)->first()->name;
        }

        return array_merge(parent::share($request), [
            'cas_user' => $cas_user,
            'cas_user_role' => $cas_user_role
        ]);
    }
}
