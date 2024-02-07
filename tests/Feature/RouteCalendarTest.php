<?php

use App\Models\CasUser;
use App\Models\Role;

test('cas user can view calendar', function() {
    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('calendar.index'))->assertRedirect(route('login'));

    $cas_user = CasUser::factory()->create([
        'role_id' => Role::factory()->create(['name' => 'User'])->id,
        'employee_number' => '123456789',
    ]);
    $response = $this->get(route('calendar.index'));
    $response->assertOk()
        ->assertSee($cas_user->name);
});
