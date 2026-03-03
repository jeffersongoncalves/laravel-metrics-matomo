<?php

namespace JeffersonGoncalves\MetricsMatomo\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\MetricsMatomo\Data\LiveCounter;
use JeffersonGoncalves\MetricsMatomo\Data\ReportRow;
use JeffersonGoncalves\MetricsMatomo\Data\VisitSummary;
use JeffersonGoncalves\MetricsMatomo\Enums\Period;
use JeffersonGoncalves\MetricsMatomo\Queries\ReportQuery;

/**
 * @method static VisitSummary visitsSummary(Period $period = Period::Day, string $date = 'today', ?int $siteId = null)
 * @method static list<ReportRow> pageUrls(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> pageTitles(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> entryPages(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> exitPages(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> downloads(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> outlinks(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> siteSearchKeywords(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> referrerTypes(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> searchEngines(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> keywords(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> websites(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> socials(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> campaigns(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> countries(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> cities(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> regions(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> continents(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> deviceTypes(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> browsers(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> osFamilies(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> resolutions(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> eventCategories(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> eventActions(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static list<ReportRow> eventNames(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null)
 * @method static array<int|string, mixed> goals(Period $period = Period::Day, string $date = 'today', ?int $siteId = null)
 * @method static array<int|string, mixed> goal(int $goalId, Period $period = Period::Day, string $date = 'today', ?int $siteId = null)
 * @method static LiveCounter liveCounters(int $lastMinutes = 30, ?int $siteId = null)
 * @method static list<array<string, mixed>> liveVisitors(int $count = 10, ?int $siteId = null)
 * @method static array<int|string, mixed> report(ReportQuery $query)
 * @method static ReportQuery query(string $module, string $method)
 *
 * @see \JeffersonGoncalves\MetricsMatomo\Matomo
 */
class Matomo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'matomo';
    }
}
