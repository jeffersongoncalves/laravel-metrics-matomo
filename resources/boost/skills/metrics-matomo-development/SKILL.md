---
name: metrics-matomo-development
description: Development patterns for the laravel-metrics-matomo package - Matomo Analytics Reporting API client with query builders, DTOs, and enums
---

## When to use this skill

- Adding new Matomo API module methods to the client
- Creating new DTOs for API responses
- Modifying the ReportQuery builder
- Writing tests for the Matomo API integration

## Setup

Package: `jeffersongoncalves/laravel-metrics-matomo`
Namespace: `JeffersonGoncalves\MetricsMatomo`
Requires: PHP ^8.2, Laravel ^11.0|^12.0, spatie/laravel-settings ^3.0

```bash
composer require jeffersongoncalves/laravel-metrics-matomo
php artisan vendor:publish --tag=metrics-matomo-settings-migrations
php artisan migrate
```

### Settings (spatie/laravel-settings)

`MatomoSettings` class (group: `metrics-matomo`): `api_token` (string), `site_id` (int), `base_url` (string), `timezone` (string). Note: `site_id` is `int` (Matomo uses numeric IDs). Auto-registered by the service provider.

## API Client usage

`MatomoClient` - Low-level HTTP client. All requests GET to `{base_url}/index.php` with `token_auth` query param. Auto-adds `module=API`, `format=json`.

Error handling: 401 -> `AuthenticationException`, 429 -> `RateLimitException`, empty token/url -> `AuthenticationException`, API error (`result=error`) -> `MatomoException::apiError()`, other -> `MatomoException::fromResponse()`.

### Matomo Facade

```php
use JeffersonGoncalves\MetricsMatomo\Facades\Matomo;
use JeffersonGoncalves\MetricsMatomo\Enums\Period;

// VisitsSummary - returns VisitSummary DTO
$summary = Matomo::visitsSummary(Period::Day, 'today', siteId: 1);

// Actions - all return list<ReportRow>
$pages = Matomo::pageUrls(Period::Month, 'today', limit: 10);
$titles = Matomo::pageTitles();
$entries = Matomo::entryPages();
$exits = Matomo::exitPages();
$downloads = Matomo::downloads();
$outlinks = Matomo::outlinks();
$search = Matomo::siteSearchKeywords();

// Referrers - all return list<ReportRow>
$types = Matomo::referrerTypes();
$engines = Matomo::searchEngines();
$keywords = Matomo::keywords();
$websites = Matomo::websites();
$socials = Matomo::socials();
$campaigns = Matomo::campaigns();

// UserCountry - all return list<ReportRow>
$countries = Matomo::countries(Period::Month, 'today', limit: 20);
$cities = Matomo::cities();
$regions = Matomo::regions();
$continents = Matomo::continents();

// DevicesDetection / Resolution - all return list<ReportRow>
$devices = Matomo::deviceTypes();
$browsers = Matomo::browsers();
$os = Matomo::osFamilies();
$resolutions = Matomo::resolutions();

// Events - all return list<ReportRow>
$categories = Matomo::eventCategories();
$actions = Matomo::eventActions();
$names = Matomo::eventNames();

// Goals - return raw arrays
$goals = Matomo::goals(Period::Day, 'today');
$goal = Matomo::goal(1, Period::Day, 'today');

// Live
$counters = Matomo::liveCounters(lastMinutes: 30);  // LiveCounter DTO
$visitors = Matomo::liveVisitors(count: 10);         // raw array
```

All report methods share signature: `(Period $period, string $date, ?int $limit, ?int $siteId)`. Default siteId from `MatomoSettings`.

## Query builders

### ReportQuery

Generic builder for any Matomo Reporting API method. Create via `Matomo::query()` or `ReportQuery::make()`.

```php
$query = Matomo::query('Actions', 'getPageUrls')
    ->site(1)
    ->period(Period::Month)
    ->date('2024-01-01')
    ->dateRange('2024-01-01', '2024-01-31')  // sets period=range
    ->segment('browserCode==FF')
    ->limit(50)
    ->sortBy('nb_visits', 'desc')
    ->expanded()
    ->flat()
    ->label('/blog')
    ->showColumns('nb_visits', 'nb_uniq_visitors')
    ->hideColumns('bounce_rate')
    ->language('pt')
    ->param('idGoal', 1);  // arbitrary extra param
$results = Matomo::report($query);
```

Key: `dateRange()` sets `period=range`, date as `from,to`. Default `idSite` from settings. `param()` adds arbitrary params.

## DTOs

All use readonly constructor properties with `fromArray()` / `toArray()`:

- **VisitSummary** - `nbVisits`, `nbUniqVisitors`, `nbActions`, `nbUsers`, `bounceCount`, `bounceRate` (float, parsed from %), `sumVisitLength`, `maxActions`, `nbActionsPerVisit`, `avgTimeOnSite`
- **LiveCounter** - `visits`, `actions`, `visitors`, `visitsConverted`
- **ReportRow** - `label`, `nbVisits`, `nbUniqVisitors`, `nbActions`, `extra[]` (all remaining keys)

## Enums

```php
Period::Day, Period::Week, Period::Month, Period::Year, Period::Range
Format::Json, Format::Xml, Format::Csv, Format::Tsv, Format::Html, Format::Rss
```

## Configuration

`MatomoServiceProvider` (extends `PackageServiceProvider`): package name `metrics-matomo`. Registers `MatomoSettings` in settings config, `MatomoClient` and `Matomo` as singletons. Publishes migrations tag: `metrics-matomo-settings-migrations`. Auto-discovered via composer.json.

### Adding new API modules

For report methods returning `list<ReportRow>`, use private `callReport()`:

```php
public function newReport(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
{
    return $this->callReport('ModuleName', 'getMethodName', $period, $date, $limit, $siteId);
}
```

For summary methods returning raw arrays, use private `call()`:

```php
public function newSummary(Period $period = Period::Day, string $date = 'today', ?int $siteId = null): array
{
    return $this->call('ModuleName', 'get', $period, $date, $siteId);
}
```

Then add `@method` docblock to the Facade class.

## Testing patterns

```php
// Mock facade
Matomo::shouldReceive('visitsSummary')
    ->andReturn(VisitSummary::fromArray([
        'nb_visits' => 100, 'nb_uniq_visitors' => 80, 'nb_actions' => 250,
        'nb_users' => 0, 'bounce_count' => 30, 'bounce_rate' => '30%',
        'sum_visit_length' => 5000, 'max_actions' => 15,
        'nb_actions_per_visit' => 2.5, 'avg_time_on_site' => 50,
    ]));

// Mock HTTP
Http::fake([
    'matomo.example.com/index.php*' => Http::response([
        ['label' => '/home', 'nb_visits' => 100, 'nb_uniq_visitors' => 80, 'nb_actions' => 150],
    ]),
]);

// Test query builder
$query = ReportQuery::make('Actions', 'getPageUrls')->period(Period::Month)->limit(10)->flat();
$params = $query->toQueryParams();
expect($params['method'])->toBe('Actions.getPageUrls');
expect($params['period'])->toBe('month');
expect($params['flat'])->toBe(1);
```

```bash
vendor/bin/pest
vendor/bin/phpstan analyse
vendor/bin/pint
```
