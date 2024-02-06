<?php

namespace App\Contracts;

interface CasAuthInterface
{
    public function getCasUser(): array;
}
