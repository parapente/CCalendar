<?php

use App\Contracts\CasAuthInterface;
use App\Services\TestCasAuthService;
use App\Models\CasUser;
use App\Models\Role;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class, LazilyRefreshDatabase::class)
    ->beforeEach(function () {
        $this->app->bind(CasAuthInterface::class, TestCasAuthService::class);
    })
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function cas_login_user(CasUser $user)
{
    test()->mock(CasAuthInterface::class, function ($mock) use ($user) {
        $role = Role::find($user->role_id);

        $mock->shouldReceive('getCasUser')
            ->andReturn([$user, $role->name]);
        $mock->shouldReceive('authenticate')
            ->andReturn(null);
    });
}
