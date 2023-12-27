<?php

use App\Models\User;

test('profile information can be updated', function () {
    $this->actingAs($user = User::factory()->create());

    $response = $this->put(route('user-profile-information.update'), [
        'name' => 'Test Name',
        'username' => 'test@example.com',
    ]);

    expect($user->fresh())
        ->name->toEqual('Test Name')
        ->username->toEqual('test@example.com');
});
