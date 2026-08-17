<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CasUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class UserToggleActiveController extends Controller
{
    public function __invoke(Request $request, string $id, string $type): RedirectResponse
    {
        if ($request->user()->id === (int) $id && $type === 'admin') {
            return back()->withErrors(['error' => 'Δεν μπορείτε να απενεργοποιήσετε τον εαυτό σας']);
        }

        if ($type === 'admin') {
            $user = User::findOrFail($id);
        } elseif ($type === 'cas') {
            $user = CasUser::findOrFail($id);
        } else {
            abort(404);
        }

        $user->update(['active' => ! $user->active]);

        $status = $user->active ? 'ενεργοποιήθηκε' : 'απενεργοποιήθηκε';

        return to_route('administrator.user.index')
            ->with('flash.bannerStyle', 'success')
            ->with('flash.banner', "Ο χρήστης {$status} επιτυχώς");
    }
}
