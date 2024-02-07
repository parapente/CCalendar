<?php

use App\Models\CasUser;
use App\Models\Role;
use App\Models\User;

test('admin can create a new user', function() {
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get(route('administrator.user.create'));
    $response->assertRedirect(route('login'));

    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('administrator.user.create'));
    $response->assertOk();
    $response = $this->actingAs($user)->post(route('administrator.user.store'), [
        'name' => 'Test User',
        'username' => 'test_user',
        'type' => 'admin',
        'password' => '<PASSWORD>',
        'password_confirmation' => '<PASSWORD>',
    ]);
    $response->assertRedirect(route('administrator.user.index'));
    $response->assertSessionHas('flash.bannerStyle', 'success');
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'username' => 'test_user',
    ]);

    // Δοκίμασε τώρα για cas user
    $role = Role::factory()->create(['name' => 'User']);
    $response = $this->actingAs($user)->post(route('administrator.user.store'), [
        'name' => 'Test Cas User',
        'username' => 'test_cas_user',
        'type' => 'cas',
        'employee_number' => '123456789',
        'role_id' => $role->id,
        'password' => null,
    ]);
    $response->assertRedirect(route('administrator.user.index'));
    $response->assertSessionHas('flash.bannerStyle', 'success');
    $this->assertDatabaseHas('cas_users', [
        'name' => 'Test Cas User',
        'username' => 'test_cas_user',
        'role_id' => $role->id,
        'employee_number' => '123456789',
    ]);
});

test('admin can update a user', function() {
    $admin = User::factory()->create();
    $user = User::factory()->create();

    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get(route('administrator.user.edit', [$user, 'admin']));
    $response->assertRedirect(route('login'));

    $response = $this->actingAs($admin)->get(route('administrator.user.edit', [$user, 'admin']));
    $response->assertOk();
    $response = $this->actingAs($admin)->put(route('administrator.user.update', [$user, 'admin']), [
        'name' => 'Test User',
        'username' => 'test_user',
        'type' => 'admin',
        'password' => null,
    ]);
    $response->assertRedirect(route('administrator.user.index'));
    $response->assertSessionHas('flash.bannerStyle', 'success');
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'username' => 'test_user',
    ]);

    // Δοκίμασε τώρα για cas user
    $role = Role::factory()->create(['name' => 'User']);
    $role_supervisor = Role::factory()->create(['name' => 'Supervisor']);
    $cas_user = CasUser::factory()->create([
        'role_id' => $role->id,
        'employee_number' => '123456789',
    ]);
    $response = $this->actingAs($admin)->put(route('administrator.user.update', [$cas_user, 'cas']), [
        'name' => 'Test User',
        'username' => 'test_user',
        'type' => 'cas',
        'employee_number' => '111111',
        'role_id' => $role_supervisor->id,
        'password' => null,
    ]);
    $response->assertRedirect(route('administrator.user.index'));
    $response->assertSessionHas('flash.bannerStyle', 'success');
    $this->assertDatabaseHas('cas_users', [
        'name' => 'Test User',
        'username' => 'test_user',
        'employee_number' => '111111',
        'role_id' => $role_supervisor->id,
    ]);
});
