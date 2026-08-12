<?php

declare(strict_types=1);

arch()
    ->expect('App')
    ->toUseStrictTypes()
    ->toBeCasedCorrectly()
    ->toUseStrictEquality()
    ->not->toUse(['die', 'dd', 'dump', 'sleep', 'usleep', 'exit', 'var_dump', 'print_r']);

    arch()
    ->expect('App')
    ->toBeClasses()
    ->ignoring([
        \App\Contracts\CasAuthInterface::class,
        \App\Actions\Fortify\PasswordValidationRules::class,
        ])
    ->toBeFinal()
    ->ignoring([
        \App\Actions\Fortify\PasswordValidationRules::class,
        \App\Contracts\CasAuthInterface::class,
        \App\Http\Controllers\Controller::class
        ]);

arch()
    ->expect('App\Models')
    ->toExtend(\Illuminate\Database\Eloquent\Model::class)
    ->ignoring(\App\Models\User::class);

arch()
    ->expect('App\Contracts')
    ->toBeInterfaces();

arch()->preset()->php();
arch()->preset()->security();
arch()->preset()->laravel();
