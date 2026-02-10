<?php

declare(strict_types=1);

namespace Src\Shared\Core\Domain\Util;

use Illuminate\Support\Facades\Log;
use Src\Shared\Core\Domain\Exceptions\DatabaseException;
use Throwable;

trait DbKickable
{
    /**
     * @template T
     *
     * @param callable():T $callback
     *
     * @return T
     *
     * @throws DatabaseException
     */
    public function kick(callable $callback, ?string $exception = DatabaseException::class, ?string $logLevel = null)
    {
        try {
            return $callback();
        } catch (Throwable $throwable) {
            if (! $logLevel) {
                Log::error($throwable->getMessage());
            } else {
                Log::log($logLevel, $throwable->getMessage());
            }
            if (class_exists((string) $exception)) {
                throw new $exception();
            }

            throw new DatabaseException();
        }
    }
}
