<?php

use App\Contracts\CasAuthInterface;
use App\Models\CasUser;
use App\Models\Role;
use App\Services\TestCasAuthService;

test('cas user can view calendar', function() {
    // Χωρίς σύνδεση θα πρέπει να μας επαναφέρει στο login
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $this->get(route('calendar.index'))
        ->assertRedirect(config('cas.cas_client_service') . config('cas.cas_uri'));

    $cas_user = CasUser::factory()->create([
        'role_id' => Role::factory()->create(['name' => 'User'])->id,
        'employee_number' => '123456789',
    ]);

    $this->mock(CasAuthInterface::class, function ($mock) use ($cas_user) {
        $mock->shouldReceive('getCasUser')
            ->andReturn([$cas_user, 'User']);
        $mock->shouldReceive('authenticate')
            ->andReturn(null);
    });

    $response = $this->get(route('calendar.index'));
    $response->assertOk()
        ->assertSee($cas_user->name);
});
