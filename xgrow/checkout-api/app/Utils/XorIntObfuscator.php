<?php

namespace App\Utils;

use Illuminate\Support\Facades\Crypt;

/**
 * This is a basic XOR cipher to obfuscate INTEGERS (like IDs), it uses two magic numbers to execute basic math over an
 * integer and return another number.
 *
 * The multiplier increases entropy of the original given integer.
 *
 * ATTENTION: this is a basic cipher and SHALL NOT be used in places where security is a high concern and/or real
 * encryption is required
 */
class XorIntObfuscator
{
    private static int $magicMultiplier = 3221;

    private static int $magicXor = 72869;

    public static function obfuscate(int $plainValue): int
    {
        // multiply original value to increase entropy and apply XOR
        return ($plainValue * self::$magicMultiplier) ^ self::$magicXor;
    }

    public static function reveal(int $obfuscatedValue): int
    {
        // undo obfuscation steps: re-apply XOR and divide by multiplier
        return ($obfuscatedValue ^ self::$magicXor) / self::$magicMultiplier;
    }

    public static function validate(int $obfuscatedValue): bool
    {
        // basic validation checking if obfuscated value is divisible by multiplier
        return (($obfuscatedValue ^ self::$magicXor) % self::$magicMultiplier) === 0;
    }

}
