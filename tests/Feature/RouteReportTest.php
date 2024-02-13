<?php

use App\Models\CasUser;
use App\Models\Role;

test('cas users should see reports addressed to them', function() {
    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('report.index'))
        ->assertRedirect(config('cas.cas_client_service'). config('cas.cas_uri'));

    $cas_user = CasUser::factory()->user()->create([
        'employee_number' => '1234567890',
    ]);

    cas_login_user($cas_user);

    $response = $this->get(route('report.index'));
    $response
        ->assertOk()
        ->assertSee($cas_user->name);
});
