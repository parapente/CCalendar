<?php

use App\Models\User;

test('other browser sessions can be logged out', function () {
    $this->actingAs($user = User::factory()->create());

    $response = $this->delete(route('other-browser-sessions.destroy'), [
        'password' => 'password',
    ]);

    $response->assertSessionHasNoErrors();
});
