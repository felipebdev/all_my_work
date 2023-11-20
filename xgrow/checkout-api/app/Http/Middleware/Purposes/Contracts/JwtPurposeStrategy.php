<?php

namespace App\Http\Middleware\Purposes\Contracts;

use Illuminate\Http\Request;
use stdClass;

interface JwtPurposeStrategy
{
    public function getErrors(stdClass $jwtPayload, Request $request): array;
}
