<?php

namespace JeffersonGoncalves\MetricsMatomo;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\MetricsMatomo\Exceptions\AuthenticationException;
use JeffersonGoncalves\MetricsMatomo\Exceptions\MatomoException;
use JeffersonGoncalves\MetricsMatomo\Exceptions\RateLimitException;

class MatomoClient
{
    public function __construct(
        private readonly string $token,
        private readonly string $baseUrl,
    ) {}

    /**
     * @param  array<string, mixed>  $params
     * @return array<int|string, mixed>
     */
    public function request(string $method, array $params = []): array
    {
        $this->ensureToken();
        $this->ensureBaseUrl();

        $params = array_merge([
            'module' => 'API',
            'method' => $method,
            'format' => 'json',
            'token_auth' => $this->token,
        ], $params);

        $url = rtrim($this->baseUrl, '/').'/index.php';

        $response = $this->buildRequest()->get($url, $params);

        return $this->handleResponse($response);
    }

    private function buildRequest(): PendingRequest
    {
        return Http::accept('application/json');
    }

    private function ensureToken(): void
    {
        if ($this->token === '') {
            throw AuthenticationException::missingToken();
        }
    }

    private function ensureBaseUrl(): void
    {
        if ($this->baseUrl === '') {
            throw AuthenticationException::missingBaseUrl();
        }
    }

    /**
     * @return array<int|string, mixed>
     */
    private function handleResponse(Response $response): array
    {
        if ($response->status() === 401) {
            throw AuthenticationException::invalidToken();
        }

        if ($response->status() === 429) {
            throw RateLimitException::exceeded();
        }

        if (! $response->successful()) {
            throw MatomoException::fromResponse($response->status(), $response->body());
        }

        $data = $response->json();

        if (is_array($data) && isset($data['result']) && $data['result'] === 'error') {
            throw MatomoException::apiError($data['message'] ?? 'Unknown API error');
        }

        return is_array($data) ? $data : [];
    }
}
