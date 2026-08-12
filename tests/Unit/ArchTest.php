<?php

declare(strict_types=1);
use App\Actions\Fortify\PasswordValidationRules;
use App\Contracts\CasAuthInterface;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

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
        CasAuthInterface::class,
        PasswordValidationRules::class,
    ])
    ->toBeFinal()
    ->ignoring([
        PasswordValidationRules::class,
        CasAuthInterface::class,
        Controller::class,
    ]);

arch()
    ->expect('App\Models')
    ->toExtend(Model::class)
    ->ignoring(User::class);

arch()
    ->expect('App\Contracts')
    ->toBeInterfaces();

arch()->preset()->php();
arch()->preset()->security();
arch()->preset()->laravel();
