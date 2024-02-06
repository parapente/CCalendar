<?php

namespace App\Http\Middleware;

use App\Contracts\CasAuthInterface;
use App\Models\CasUser;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CasUserRegistered
{
    protected $auth;

    public function __construct(CasAuthInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        [$cas_user, $cas_user_role] = $this->auth->getCasUser();

        if (!$cas_user) {
            return to_route('invalid.cas_user');
        }

        // Share data with Inertia
        $request->merge(['cas_user' => $cas_user, 'cas_user_role' => $cas_user_role]);
        Inertia::share('cas_user', $cas_user);
        Inertia::share('cas_user_role', $cas_user_role);

        return $next($request);
    }
}
