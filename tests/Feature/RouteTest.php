<?php

it('can access public routes', function($route) {
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get($route);
    $response->assertOk();
})->with([
    '/',
    'administrator/login',
]);

it('cannot access administrator routes', function($route) {
    /** @var Illuminate\Foundation\Testing\TestCase $this */
    $response = $this->get($route);
    $response->assertRedirect(route('login'));
})->with([
    'administrator',
    'administrator/calendar',
    'administrator/calendar/create',
    'administrator/calendar/overview',
    'administrator/calendar/1/edit',
    // 'administrator/cas_user/1/name',
    'administrator/events/2023/01',
    'administrator/report',
    'administrator/report/create',
    'administrator/report/1',
    'administrator/report/1/edit',
    'administrator/report/1/getAllFiles',
    'administrator/report/1/getCalendarToWord',
    'administrator/report/1/getFile/1',
    'administrator/two-factor-challenge',
    'administrator/user',
    'administrator/user/confirm-password',
    'administrator/user/confirmed-password-status',
    'administrator/user/create',
    'administrator/user/two-factor-qr-code',
    'administrator/user/two-factor-recovery-codes',
    'administrator/user/two-factor-secret-key',
    'administrator/user/1/type/1/edit',
]);
