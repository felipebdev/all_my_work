<?php

namespace App\Http\Middleware\Purposes;

use App\Http\Middleware\Purposes\Contracts\JwtPurposeStrategy;
use Illuminate\Support\Str;
use InvalidArgumentException;

class JwtPurposeFactory
{
    /**
     * @param  string  $purpose
     * @return \App\Http\Middleware\Purposes\Contracts\JwtPurposeStrategy
     * @throw InvalidArgumentException
     */
    public static function getStrategy(string $purpose): JwtPurposeStrategy
    {
        $implementationNamespace = __NAMESPACE__.'\\Implementation';
        $className = Str::of($purpose)->camel()->ucfirst();

        $fullyQualifiedClassName = "{$implementationNamespace}\\{$className}";

        if (class_exists($fullyQualifiedClassName)) {
            return new $fullyQualifiedClassName();
        }

        throw new InvalidArgumentException('Unknown purpose');
    }
}
