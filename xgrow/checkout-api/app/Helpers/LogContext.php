<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

/**
 * Class to help handle of Log context from default logger
 */
class LogContext
{

    /**
     * Get context from default logger using Reflection to access private data
     *
     * @return array
     */
    public static function get(): array
    {
        try {
            $log = app('log');

            if (!$log) {
                return [];
            }

            $reflection = new \ReflectionClass($log);
            $property = $reflection->getProperty('channels');
            $property->setAccessible(true);
            $channels = $property->getValue($log);

            $logger = array_first($channels) ?? null;

            if (!$logger) {
                return [];
            }

            $reflection2 = new \ReflectionClass($logger);
            $property = $reflection2->getProperty('context');
            $property->setAccessible(true);
            $context = $property->getValue($logger);

            return $context;
        } catch (\Throwable $exception) {
            // Ignore errors
            Log::warning('Failed to load context from default logger', [
                'exception' => [
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString(),
                ],
            ]);
            return [];
        }
    }

    /**
     * Add context to default logger
     *
     * @param  array  $context
     */
    public static function add(array $context): void
    {
        Log::withContext($context);
    }
}
