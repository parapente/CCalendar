<?php

use App\Contracts\CasAuthInterface;
use App\Models\CasUser;
use App\Models\Role;

/*
|--------------------------------------------------------------------------
| CAS Middleware - Inactive User
|--------------------------------------------------------------------------
*/

test('inactive cas user is redirected to invalid cas user page', function (): void {
    $role = Role::factory()->create(['name' => 'User']);
    $cas_user = CasUser::factory()->create([
        'role_id' => $role->id,
        'active' => false,
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('calendar.index'));
    $response->assertRedirect(route('invalid.cas_user'));
});

/*
|--------------------------------------------------------------------------
| CAS Middleware - User Not Found
|--------------------------------------------------------------------------
*/

test('cas user not found in database is redirected to invalid cas user page', function (): void {
    test()->mock(CasAuthInterface::class, function ($mock): void {
        $mock->shouldReceive('getCasUser')
            ->andReturn([null, null]);
        $mock->shouldReceive('authenticate')
            ->andReturn(null);
    });

    $response = $this->get(route('calendar.index'));
    $response->assertRedirect(route('invalid.cas_user'));
});

/*
|--------------------------------------------------------------------------
| CAS Middleware - Valid User
|--------------------------------------------------------------------------
*/

test('active cas user can access protected routes', function (): void {
    $role = Role::factory()->create(['name' => 'User']);
    $cas_user = CasUser::factory()->create([
        'role_id' => $role->id,
        'active' => true,
        'employee_number' => '123456789',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('calendar.index'));
    $response->assertOk();
});
