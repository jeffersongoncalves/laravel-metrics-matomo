<?php

namespace JeffersonGoncalves\MetricsMatomo\Exceptions;

class RateLimitException extends MatomoException
{
    public static function exceeded(): self
    {
        return new self('Matomo API rate limit exceeded. Try again later.', 429);
    }
}
