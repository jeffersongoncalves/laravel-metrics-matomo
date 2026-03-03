<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\MetricsMatomo\Exceptions\AuthenticationException;
use JeffersonGoncalves\MetricsMatomo\Exceptions\MatomoException;
use JeffersonGoncalves\MetricsMatomo\Exceptions\RateLimitException;
use JeffersonGoncalves\MetricsMatomo\Matomo;
use JeffersonGoncalves\MetricsMatomo\MatomoClient;

it('resolves matomo from container', function () {
    $matomo = app('matomo');

    expect($matomo)->toBeInstanceOf(Matomo::class);
});

it('throws exception when token is empty', function () {
    $client = new MatomoClient(token: '', baseUrl: 'https://matomo.example.com');
    $client->request('VisitsSummary.get', ['idSite' => 1, 'period' => 'day', 'date' => 'today']);
})->throws(AuthenticationException::class, 'Matomo API token is not configured');

it('throws exception when base url is empty', function () {
    $client = new MatomoClient(token: 'test-token', baseUrl: '');
    $client->request('VisitsSummary.get', ['idSite' => 1, 'period' => 'day', 'date' => 'today']);
})->throws(AuthenticationException::class, 'Matomo base URL is not configured');

it('throws exception on 401 response', function () {
    Http::fake([
        'matomo.example.com/*' => Http::response(['result' => 'error', 'message' => 'Unauthorized'], 401),
    ]);

    $client = new MatomoClient(token: 'invalid-token', baseUrl: 'https://matomo.example.com');
    $client->request('VisitsSummary.get', ['idSite' => 1, 'period' => 'day', 'date' => 'today']);
})->throws(AuthenticationException::class);

it('throws exception on 429 response', function () {
    Http::fake([
        'matomo.example.com/*' => Http::response(['error' => 'Rate limit exceeded'], 429),
    ]);

    $client = new MatomoClient(token: 'test-token', baseUrl: 'https://matomo.example.com');
    $client->request('VisitsSummary.get', ['idSite' => 1, 'period' => 'day', 'date' => 'today']);
})->throws(RateLimitException::class);

it('throws exception on matomo api error response', function () {
    Http::fake([
        'matomo.example.com/*' => Http::response([
            'result' => 'error',
            'message' => 'You must specify a value for idSite.',
        ]),
    ]);

    $client = new MatomoClient(token: 'test-token', baseUrl: 'https://matomo.example.com');
    $client->request('VisitsSummary.get');
})->throws(MatomoException::class, 'You must specify a value for idSite');

it('fetches visits summary', function () {
    Http::fake([
        'matomo.example.com/*' => Http::response([
            'nb_visits' => 1234,
            'nb_uniq_visitors' => 567,
            'nb_actions' => 3456,
            'nb_users' => 89,
            'bounce_count' => 200,
            'bounce_rate' => '16%',
            'sum_visit_length' => 98765,
            'max_actions' => 50,
            'nb_actions_per_visit' => 2.8,
            'avg_time_on_site' => 120,
        ]),
    ]);

    $matomo = app('matomo');
    $summary = $matomo->visitsSummary();

    expect($summary->nbVisits)->toBe(1234)
        ->and($summary->nbUniqVisitors)->toBe(567)
        ->and($summary->nbActions)->toBe(3456)
        ->and($summary->bounceRate)->toBe(16.0);
});

it('fetches page urls', function () {
    Http::fake([
        'matomo.example.com/*' => Http::response([
            ['label' => '/home', 'nb_visits' => 100, 'nb_uniq_visitors' => 80, 'nb_actions' => 150],
            ['label' => '/about', 'nb_visits' => 50, 'nb_uniq_visitors' => 40, 'nb_actions' => 60],
        ]),
    ]);

    $matomo = app('matomo');
    $pages = $matomo->pageUrls();

    expect($pages)->toHaveCount(2)
        ->and($pages[0]->label)->toBe('/home')
        ->and($pages[0]->nbVisits)->toBe(100)
        ->and($pages[1]->label)->toBe('/about');
});

it('fetches referrer types', function () {
    Http::fake([
        'matomo.example.com/*' => Http::response([
            ['label' => 'Direct Entry', 'nb_visits' => 500, 'nb_uniq_visitors' => 400, 'nb_actions' => 1000],
            ['label' => 'Search Engines', 'nb_visits' => 300, 'nb_uniq_visitors' => 250, 'nb_actions' => 600],
        ]),
    ]);

    $matomo = app('matomo');
    $referrers = $matomo->referrerTypes();

    expect($referrers)->toHaveCount(2)
        ->and($referrers[0]->label)->toBe('Direct Entry')
        ->and($referrers[1]->label)->toBe('Search Engines');
});

it('fetches countries', function () {
    Http::fake([
        'matomo.example.com/*' => Http::response([
            ['label' => 'Brazil', 'nb_visits' => 800, 'nb_uniq_visitors' => 600, 'nb_actions' => 2000, 'code' => 'BR'],
            ['label' => 'United States', 'nb_visits' => 200, 'nb_uniq_visitors' => 150, 'nb_actions' => 500, 'code' => 'US'],
        ]),
    ]);

    $matomo = app('matomo');
    $countries = $matomo->countries();

    expect($countries)->toHaveCount(2)
        ->and($countries[0]->label)->toBe('Brazil')
        ->and($countries[0]->extra)->toHaveKey('code', 'BR');
});

it('fetches browsers', function () {
    Http::fake([
        'matomo.example.com/*' => Http::response([
            ['label' => 'Chrome', 'nb_visits' => 600, 'nb_uniq_visitors' => 500, 'nb_actions' => 1200],
            ['label' => 'Firefox', 'nb_visits' => 200, 'nb_uniq_visitors' => 180, 'nb_actions' => 400],
        ]),
    ]);

    $matomo = app('matomo');
    $browsers = $matomo->browsers();

    expect($browsers)->toHaveCount(2)
        ->and($browsers[0]->label)->toBe('Chrome')
        ->and($browsers[1]->label)->toBe('Firefox');
});

it('fetches live counters', function () {
    Http::fake([
        'matomo.example.com/*' => Http::response([
            ['visits' => 42, 'actions' => 156, 'visitors' => 38, 'visitsConverted' => 5],
        ]),
    ]);

    $matomo = app('matomo');
    $counters = $matomo->liveCounters();

    expect($counters->visits)->toBe(42)
        ->and($counters->actions)->toBe(156)
        ->and($counters->visitors)->toBe(38)
        ->and($counters->visitsConverted)->toBe(5);
});

it('fetches live visitors', function () {
    Http::fake([
        'matomo.example.com/*' => Http::response([
            ['idVisit' => '1', 'visitorId' => 'abc123', 'actions' => 5],
            ['idVisit' => '2', 'visitorId' => 'def456', 'actions' => 3],
        ]),
    ]);

    $matomo = app('matomo');
    $visitors = $matomo->liveVisitors(count: 2);

    expect($visitors)->toHaveCount(2)
        ->and($visitors[0])->toHaveKey('idVisit', '1');
});

it('executes generic report query', function () {
    Http::fake([
        'matomo.example.com/*' => Http::response([
            ['label' => 'test', 'nb_visits' => 100, 'nb_uniq_visitors' => 80, 'nb_actions' => 200],
        ]),
    ]);

    $matomo = app('matomo');
    $query = $matomo->query('VisitsSummary', 'get')
        ->period(\JeffersonGoncalves\MetricsMatomo\Enums\Period::Month)
        ->date('2026-02-01')
        ->limit(50);

    $result = $matomo->report($query);

    expect($result)->toHaveCount(1);
});
