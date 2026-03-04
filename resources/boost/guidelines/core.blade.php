## Laravel Metrics Matomo

### Overview
Laravel package for Matomo Analytics Reporting API. Provides a fluent API client with typed methods for VisitsSummary, Actions, Referrers, UserCountry, DevicesDetection, Events, Goals, and Live data. Uses `spatie/laravel-settings` for configuration storage. Namespace: `JeffersonGoncalves\MetricsMatomo`.

### Key Concepts
- **Facade**: `Matomo` facade (`JeffersonGoncalves\MetricsMatomo\Facades\Matomo`) bound to `'matomo'`
- **Settings**: `MatomoSettings` (spatie/laravel-settings) with group `metrics-matomo`
- **Client**: `MatomoClient` uses Laravel HTTP client with `token_auth` query parameter
- **Query Builder**: `ReportQuery` for generic Matomo Reporting API calls

### API Client
The `Matomo` class wraps Matomo Reporting API modules with typed methods.

@verbatim
<code-snippet name="facade-usage" lang="php">
use JeffersonGoncalves\MetricsMatomo\Facades\Matomo;
use JeffersonGoncalves\MetricsMatomo\Enums\Period;

// VisitsSummary
$summary = Matomo::visitsSummary(Period::Day, 'today');  // returns VisitSummary DTO

// Actions
$pages = Matomo::pageUrls(Period::Month, 'today', limit: 10);  // returns ReportRow[]
$titles = Matomo::pageTitles();
$entries = Matomo::entryPages();
$exits = Matomo::exitPages();
$downloads = Matomo::downloads();
$outlinks = Matomo::outlinks();
$search = Matomo::siteSearchKeywords();

// Referrers
$types = Matomo::referrerTypes();
$engines = Matomo::searchEngines();
$socials = Matomo::socials();
$campaigns = Matomo::campaigns();

// Geography
$countries = Matomo::countries(Period::Month, 'today');
$cities = Matomo::cities();

// Devices
$devices = Matomo::deviceTypes();
$browsers = Matomo::browsers();
$os = Matomo::osFamilies();

// Events
$categories = Matomo::eventCategories();
$actions = Matomo::eventActions();

// Goals
$goals = Matomo::goals();
$goal = Matomo::goal(goalId: 1);

// Live
$counters = Matomo::liveCounters(lastMinutes: 30);  // returns LiveCounter DTO
$visitors = Matomo::liveVisitors(count: 10);
</code-snippet>
@endverbatim

### DTOs
- `VisitSummary` - nbVisits, nbUniqVisitors, nbActions, nbUsers, bounceCount, bounceRate, sumVisitLength, maxActions, nbActionsPerVisit, avgTimeOnSite
- `LiveCounter` - visits, actions, visitors, visitsConverted
- `ReportRow` - label, nbVisits, nbUniqVisitors, nbActions, extra[]

All DTOs implement `fromArray(array $data)` and `toArray()`.

### Enums

@verbatim
<code-snippet name="enums" lang="php">
use JeffersonGoncalves\MetricsMatomo\Enums\{Period, Format};

Period::Day, Period::Week, Period::Month, Period::Year, Period::Range
Format::Json, Format::Xml, Format::Csv, Format::Tsv, Format::Html, Format::Rss
</code-snippet>
@endverbatim

### ReportQuery Builder

@verbatim
<code-snippet name="report-query" lang="php">
use JeffersonGoncalves\MetricsMatomo\Facades\Matomo;
use JeffersonGoncalves\MetricsMatomo\Enums\Period;

$query = Matomo::query('Actions', 'getPageUrls')
    ->site(1)
    ->period(Period::Month)
    ->date('2024-01-01')
    ->dateRange('2024-01-01', '2024-01-31')
    ->segment('browserCode==FF')
    ->limit(50)
    ->sortBy('nb_visits', 'desc')
    ->expanded()
    ->flat()
    ->label('/blog')
    ->showColumns('nb_visits', 'nb_uniq_visitors')
    ->hideColumns('bounce_rate')
    ->language('pt')
    ->param('customKey', 'value');

$results = Matomo::report($query);
</code-snippet>
@endverbatim

### Configuration
Settings stored via `MatomoSettings` (spatie/laravel-settings, group: `metrics-matomo`):
- `api_token` (string) - Matomo auth token
- `site_id` (int) - Default site ID
- `base_url` (string) - Matomo instance URL
- `timezone` (string) - Default timezone

Publish migrations: `php artisan vendor:publish --tag=metrics-matomo-settings-migrations`

### Conventions
- Report methods return `ReportRow[]` (list of DTOs)
- Methods accepting `?int $siteId = null` fall back to `MatomoSettings::site_id`
- API calls use `Module.method` format (e.g., `Actions.getPageUrls`)
- Client sends all params as GET query parameters to `{base_url}/index.php`
- Exceptions: `AuthenticationException` (401/missing), `RateLimitException` (429), `MatomoException` (generic + API errors)
