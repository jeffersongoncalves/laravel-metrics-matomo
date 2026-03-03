<div class="filament-hidden">

![Laravel Metrics Matomo](https://raw.githubusercontent.com/jeffersongoncalves/laravel-metrics-matomo/main/art/jeffersongoncalves-laravel-metrics-matomo.png)

</div>

# Laravel Metrics Matomo

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-metrics-matomo.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-metrics-matomo)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-metrics-matomo/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-metrics-matomo/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![PHPStan](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-metrics-matomo/phpstan.yml?branch=main&label=PHPStan&style=flat-square)](https://github.com/jeffersongoncalves/laravel-metrics-matomo/actions?query=workflow%3APHPStan+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-metrics-matomo.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-metrics-matomo)

Laravel package to interact with the [Matomo Analytics](https://matomo.org) Reporting API. Fetch visits, pageviews, referrers, devices, countries, live visitors, and generate custom reports for dashboards.

Settings are stored in the database via [spatie/laravel-settings](https://github.com/spatie/laravel-settings) — no config files needed.

## Installation

```bash
composer require jeffersongoncalves/laravel-metrics-matomo
```

Run migrations to create the settings:

```bash
php artisan migrate
```

## Configuration

After migration, the settings are seeded from environment variables:

```env
MATOMO_API_TOKEN=your-token-auth
MATOMO_SITE_ID=1
MATOMO_BASE_URL=https://your-matomo-instance.com
MATOMO_TIMEZONE=UTC
```

You can also update settings programmatically:

```php
use JeffersonGoncalves\MetricsMatomo\Settings\MatomoSettings;

$settings = app(MatomoSettings::class);
$settings->api_token = 'new-token';
$settings->site_id = 2;
$settings->base_url = 'https://matomo.example.com';
$settings->timezone = 'America/Sao_Paulo';
$settings->save();
```

## Usage

### Using the Facade

```php
use JeffersonGoncalves\MetricsMatomo\Facades\Matomo;
use JeffersonGoncalves\MetricsMatomo\Enums\Period;
```

### Visits Summary

```php
// Today's summary
$summary = Matomo::visitsSummary();
echo $summary->nbVisits;         // 1234
echo $summary->nbUniqVisitors;   // 567
echo $summary->nbActions;        // 3456
echo $summary->bounceRate;       // 16.0
echo $summary->avgTimeOnSite;    // 120.5

// Monthly summary
$summary = Matomo::visitsSummary(Period::Month, 'today');

// Specific date
$summary = Matomo::visitsSummary(Period::Day, '2026-02-15');
```

### Top Pages

```php
// Top 10 page URLs
$pages = Matomo::pageUrls(limit: 10);
foreach ($pages as $page) {
    echo $page->label . ': ' . $page->nbVisits . ' visits';
}

// Page titles
$titles = Matomo::pageTitles(Period::Month, 'today', limit: 20);

// Entry and exit pages
$entry = Matomo::entryPages(limit: 10);
$exit = Matomo::exitPages(limit: 10);
```

### Referrers

```php
// Referrer types (Direct, Search, Social, etc.)
$types = Matomo::referrerTypes();

// Search engines
$engines = Matomo::searchEngines(Period::Month, 'today');

// Keywords
$keywords = Matomo::keywords(limit: 20);

// Referring websites
$sites = Matomo::websites(limit: 10);

// Social networks
$socials = Matomo::socials();

// Campaigns
$campaigns = Matomo::campaigns(Period::Month, 'today');
```

### Geography

```php
// Countries
$countries = Matomo::countries(Period::Month, 'today');
foreach ($countries as $country) {
    echo $country->label . ': ' . $country->nbVisits;
    echo ' (' . $country->extra['code'] . ')';  // Country code
}

// Cities
$cities = Matomo::cities(limit: 10);

// Regions
$regions = Matomo::regions();

// Continents
$continents = Matomo::continents();
```

### Devices & Technology

```php
// Device types (Desktop, Mobile, Tablet)
$devices = Matomo::deviceTypes();

// Browsers
$browsers = Matomo::browsers(limit: 10);

// Operating systems
$os = Matomo::osFamilies();

// Screen resolutions
$resolutions = Matomo::resolutions(limit: 10);
```

### Events

```php
// Event categories
$categories = Matomo::eventCategories(Period::Month, 'today');

// Event actions
$actions = Matomo::eventActions();

// Event names
$names = Matomo::eventNames(limit: 20);
```

### Goals

```php
// All goals summary
$goals = Matomo::goals(Period::Month, 'today');

// Specific goal
$goal = Matomo::goal(goalId: 1, period: Period::Month, date: 'today');
```

### Live Visitors (Real-time)

```php
// Counters for last 30 minutes
$counters = Matomo::liveCounters();
echo $counters->visits;          // 42
echo $counters->actions;         // 156
echo $counters->visitors;        // 38
echo $counters->visitsConverted; // 5

// Counters for last 60 minutes
$counters = Matomo::liveCounters(lastMinutes: 60);

// Last visitors details
$visitors = Matomo::liveVisitors(count: 10);
```

### Custom Reports (Query Builder)

Build flexible reports using the fluent query builder:

```php
use JeffersonGoncalves\MetricsMatomo\Enums\Period;

// Custom query with segment
$query = Matomo::query('VisitsSummary', 'get')
    ->period(Period::Month)
    ->date('2026-02-01')
    ->segment('referrerName==twitter.com')
    ->limit(50);

$result = Matomo::report($query);

// Date range query
$query = Matomo::query('Actions', 'getPageUrls')
    ->dateRange('2026-01-01', '2026-01-31')
    ->sortBy('nb_visits', 'desc')
    ->limit(20)
    ->flat();

$result = Matomo::report($query);

// Show/hide specific columns
$query = Matomo::query('VisitsSummary', 'get')
    ->period(Period::Day)
    ->date('today')
    ->showColumns('nb_visits', 'nb_uniq_visitors', 'bounce_rate');

$result = Matomo::report($query);

// With extra parameters
$query = Matomo::query('Goals', 'get')
    ->period(Period::Month)
    ->date('today')
    ->param('idGoal', 1);

$result = Matomo::report($query);
```

### Using DateTime Objects

```php
use Carbon\Carbon;

$summary = Matomo::visitsSummary(
    period: Period::Day,
    date: Carbon::yesterday()->format('Y-m-d'),
);

$query = Matomo::query('Actions', 'getPageUrls')
    ->dateRange(Carbon::now()->subDays(30), Carbon::now());

$result = Matomo::report($query);
```

## Available Enums

### Period
`Day`, `Week`, `Month`, `Year`, `Range`

### Format
`Json`, `Xml`, `Csv`, `Tsv`, `Html`, `Rss`

## Available API Methods

| Category | Methods |
|----------|---------|
| **Visits** | `visitsSummary` |
| **Pages** | `pageUrls`, `pageTitles`, `entryPages`, `exitPages`, `downloads`, `outlinks`, `siteSearchKeywords` |
| **Referrers** | `referrerTypes`, `searchEngines`, `keywords`, `websites`, `socials`, `campaigns` |
| **Geography** | `countries`, `cities`, `regions`, `continents` |
| **Devices** | `deviceTypes`, `browsers`, `osFamilies`, `resolutions` |
| **Events** | `eventCategories`, `eventActions`, `eventNames` |
| **Goals** | `goals`, `goal` |
| **Live** | `liveCounters`, `liveVisitors` |
| **Generic** | `query`, `report` |

## Testing

```bash
composer test
```

## Code Style

```bash
composer format
```

## Static Analysis

```bash
composer analyse
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
