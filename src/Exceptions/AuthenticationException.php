<?php

namespace JeffersonGoncalves\MetricsMatomo\Exceptions;

class AuthenticationException extends MatomoException
{
    public static function missingToken(): self
    {
        return new self('Matomo API token is not configured. Set MATOMO_API_TOKEN in your .env file.');
    }

    public static function invalidToken(): self
    {
        return new self('The provided Matomo API token is invalid.', 401);
    }

    public static function missingBaseUrl(): self
    {
        return new self('Matomo base URL is not configured. Set MATOMO_BASE_URL in your .env file.');
    }
}
