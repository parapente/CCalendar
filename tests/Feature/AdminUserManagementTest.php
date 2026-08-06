<?php

use App\Models\CasUser;
use App\Models\Role;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Admin User Toggle Active
|--------------------------------------------------------------------------
*/

test('admin can toggle active status of another admin user', function (): void {
    $admin = User::factory()->create();
    $user = User::factory()->create(['active' => true]);

    $response = $this->actingAs($admin)->post(route('administrator.user.toggleActive', [$user, 'admin']));
    $response->assertRedirect(route('administrator.user.index'));
    $user->refresh();
    expect($user->active)->toBeFalsy();
});

test('admin can toggle active status of a cas user', function (): void {
    $admin = User::factory()->create();
    $role = Role::factory()->create(['name' => 'User']);
    $cas_user = CasUser::factory()->create([
        'role_id' => $role->id,
        'active' => true,
        'employee_number' => '123456789',
    ]);

    $response = $this->actingAs($admin)->post(route('administrator.user.toggleActive', [$cas_user, 'cas']));
    $response->assertRedirect(route('administrator.user.index'));
    $cas_user->refresh();
    expect($cas_user->active)->toBeFalsy();
});

test('admin can toggle active status back on for another admin user', function (): void {
    $admin = User::factory()->create();
    $user = User::factory()->create(['active' => false]);

    $response = $this->actingAs($admin)->post(route('administrator.user.toggleActive', [$user, 'admin']));
    $response->assertRedirect(route('administrator.user.index'));
    $user->refresh();
    expect($user->active)->toBeTruthy();
});

test('admin can toggle active status back on for a cas user', function (): void {
    $admin = User::factory()->create();
    $role = Role::factory()->create(['name' => 'User']);
    $cas_user = CasUser::factory()->create([
        'role_id' => $role->id,
        'active' => false,
        'employee_number' => '123456789',
    ]);

    $response = $this->actingAs($admin)->post(route('administrator.user.toggleActive', [$cas_user, 'cas']));
    $response->assertRedirect(route('administrator.user.index'));
    $cas_user->refresh();
    expect($cas_user->active)->toBeTruthy();
});

test('admin cannot deactivate themselves', function (): void {
    $admin = User::factory()->create(['active' => true]);

    $response = $this->actingAs($admin)->post(route('administrator.user.toggleActive', [$admin, 'admin']));
    $response->assertSessionHasErrors('error');
    $admin->refresh();
    expect($admin->active)->toBeTruthy();
});

/*
|--------------------------------------------------------------------------
| Unauthenticated Access
|--------------------------------------------------------------------------
*/

test('unauthenticated user cannot toggle active of admin user', function (): void {
    $user = User::factory()->create();

    $response = $this->post(route('administrator.user.toggleActive', [$user, 'admin']));
    $response->assertRedirect(route('login'));
});

test('admin can view user edit form for admin user', function (): void {
    $admin = User::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($admin)->get(route('administrator.user.edit', [$user, 'admin']));
    $response->assertOk();
});

test('admin can view user edit form for cas user', function (): void {
    $admin = User::factory()->create();
    $role = Role::factory()->create(['name' => 'User']);
    $cas_user = CasUser::factory()->create([
        'role_id' => $role->id,
        'employee_number' => '123456789',
    ]);

    $response = $this->actingAs($admin)->get(route('administrator.user.edit', [$cas_user, 'cas']));
    $response->assertOk();
});

test('admin can update admin user with null password (keeping existing password)', function (): void {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $originalPasswordHash = $user->password;

    $response = $this->actingAs($admin)->put(route('administrator.user.update', [$user, 'admin']), [
        'name' => 'Updated Name',
        'username' => 'updated_admin',
        'type' => 'admin',
        'password' => null,
    ]);
    $response->assertRedirect(route('administrator.user.index'));
    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect($user->username)->toBe('updated_admin');
    expect($user->password)->toBe($originalPasswordHash);
});
