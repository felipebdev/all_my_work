<?php

namespace App\Http\Middleware\Purposes\Implementation;

use App\Http\Middleware\Purposes\Contracts\JwtPurposeStrategy;
use Illuminate\Http\Request;
use stdClass;

class Bank implements JwtPurposeStrategy
{

    public function getErrors(stdClass $jwtPayload, Request $request): array
    {
        $errors = [];

        if (empty($jwtPayload->user_id)) {
            $errors[] = 'user_id required';
        }

        if ($errors) {
            return $errors;
        }

        return [];
    }

}


