<?php

namespace App\Http\Middleware;

use App\Models\CasUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CasUserRegistered
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $employee_number = cas()->getAttribute('employeenumber');
        $cas_user = CasUser::where('employee_number', $employee_number)->first();

        if (!$cas_user) {
            return to_route('invalid.cas_user');
        }

        $request->merge(['cas_user' => $cas_user]);

        return $next($request);
    }
}
