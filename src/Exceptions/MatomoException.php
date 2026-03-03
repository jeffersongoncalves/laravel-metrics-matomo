<?php

namespace JeffersonGoncalves\MetricsMatomo\Exceptions;

use Exception;

class MatomoException extends Exception
{
    public static function fromResponse(int $statusCode, string $message): self
    {
        return new self("Matomo API error ({$statusCode}): {$message}", $statusCode);
    }

    public static function apiError(string $message): self
    {
        return new self("Matomo API error: {$message}");
    }
}
