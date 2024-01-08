<?php

namespace App\Http\Controllers;

use App\Models\CasUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::select(['id', 'name', 'username', DB::raw("'Administrator' as role")])->get();

        $casUsers = CasUser::select([
            'cas_users.id as id',
            'cas_users.name as name',
            'username',
            'roles.name as role'
        ])
            ->join('roles', 'roles.id', '=', 'cas_users.role_id')
            ->get();

        return Inertia::render('Admin/User/Index', compact('users', 'casUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();

        return Inertia::render('Admin/User/Create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::when(
                    $request->type === 'admin',
                    Rule::unique('users'),
                    Rule::unique('cas_users'))
            ],
            'employee_number' => ['string', 'max:255', Rule::requiredIf($request->type === 'cas')],
            'type' => ['required','string'],
            'role_id' => ['required_if:type,cas', 'integer'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'password_confirmation' => ['nullable', 'string', 'max:255', Rule::requiredIf(!is_null($request->password)), 'same:password'],
        ]);

        if ($request->type === 'admin') {
            User::create($validated);
        } elseif ($request->type === 'cas') {
            CasUser::create($validated);
        } else {
            abort(404);
        }

        return to_route('administrator.user.index')
            ->with('flash.bannerStyle', 'success')
            ->with('flash.banner', 'Ο χρήστης δημιουργήθηκε επιτυχώς');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, string $type)
    {
        if ($type === 'admin') {
            $user = User::findOrFail($id, ['id', 'name', 'username']);
            $user->role = 'Administrator';
        } else if ($type === 'cas') {
            $user = CasUser::findOrFail($id, ['id', 'name', 'username', 'role_id', 'employee_number']);
            $user->role = Role::find($user->role_id)->name;
        } else {
            abort(404);
        }

        $roles = Role::all();

        return Inertia::render('Admin/User/Edit', compact('user', 'type', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, string $type)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::when(
                    $type === 'admin',
                    Rule::unique('users')->ignore($id),
                    Rule::unique('cas_users')->ignore($id))
            ],
            'employee_number' => ['string', 'max:255', Rule::requiredIf($request->type === 'cas')],
            'role_id' => [Rule::requiredIf($type === "cas"), 'integer'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'password_confirmation' => ['nullable', 'string', 'max:255', Rule::requiredIf(!is_null($request->password)), 'same:password'],
        ]);

        if ($type === 'admin') {
            $user = User::findOrFail($id);

            // Αν δεν θέλουμε να αλλάξουμε password, τότε θα είναι null
            if (is_null($validated['password'])) {
                unset($validated['password']);
            }

            $user->fill($validated);
            $user->save();
        } elseif ($type === 'cas') {
            $user = CasUser::findOrFail($id);
            $user->fill($validated);
            $user->save();
        } else {
            abort(404);
        }

        return to_route('administrator.user.index')
            ->with('flash.bannerStyle', 'success')
            ->with('flash.banner', 'Ο χρήστης ενημερώθηκε επιτυχώς');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
