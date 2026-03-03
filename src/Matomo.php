<?php

namespace JeffersonGoncalves\MetricsMatomo;

use JeffersonGoncalves\MetricsMatomo\Data\LiveCounter;
use JeffersonGoncalves\MetricsMatomo\Data\ReportRow;
use JeffersonGoncalves\MetricsMatomo\Data\VisitSummary;
use JeffersonGoncalves\MetricsMatomo\Enums\Period;
use JeffersonGoncalves\MetricsMatomo\Queries\ReportQuery;
use JeffersonGoncalves\MetricsMatomo\Settings\MatomoSettings;

class Matomo
{
    public function __construct(
        private readonly MatomoClient $client,
    ) {}

    // =========================================================================
    // VisitsSummary
    // =========================================================================

    public function visitsSummary(Period $period = Period::Day, string $date = 'today', ?int $siteId = null): VisitSummary
    {
        /** @var array<string, mixed> $response */
        $response = $this->call('VisitsSummary', 'get', $period, $date, $siteId);

        return VisitSummary::fromArray($response);
    }

    // =========================================================================
    // Actions
    // =========================================================================

    /**
     * @return list<ReportRow>
     */
    public function pageUrls(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Actions', 'getPageUrls', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function pageTitles(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Actions', 'getPageTitles', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function entryPages(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Actions', 'getEntryPageUrls', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function exitPages(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Actions', 'getExitPageUrls', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function downloads(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Actions', 'getDownloads', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function outlinks(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Actions', 'getOutlinks', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function siteSearchKeywords(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Actions', 'getSiteSearchKeywords', $period, $date, $limit, $siteId);
    }

    // =========================================================================
    // Referrers
    // =========================================================================

    /**
     * @return list<ReportRow>
     */
    public function referrerTypes(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Referrers', 'getReferrerType', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function searchEngines(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Referrers', 'getSearchEngines', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function keywords(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Referrers', 'getKeywords', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function websites(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Referrers', 'getWebsites', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function socials(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Referrers', 'getSocials', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function campaigns(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Referrers', 'getCampaigns', $period, $date, $limit, $siteId);
    }

    // =========================================================================
    // UserCountry
    // =========================================================================

    /**
     * @return list<ReportRow>
     */
    public function countries(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('UserCountry', 'getCountry', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function cities(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('UserCountry', 'getCity', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function regions(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('UserCountry', 'getRegion', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function continents(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('UserCountry', 'getContinent', $period, $date, $limit, $siteId);
    }

    // =========================================================================
    // DevicesDetection
    // =========================================================================

    /**
     * @return list<ReportRow>
     */
    public function deviceTypes(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('DevicesDetection', 'getType', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function browsers(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('DevicesDetection', 'getBrowsers', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function osFamilies(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('DevicesDetection', 'getOsFamilies', $period, $date, $limit, $siteId);
    }

    // =========================================================================
    // Resolution
    // =========================================================================

    /**
     * @return list<ReportRow>
     */
    public function resolutions(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Resolution', 'getResolution', $period, $date, $limit, $siteId);
    }

    // =========================================================================
    // Events
    // =========================================================================

    /**
     * @return list<ReportRow>
     */
    public function eventCategories(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Events', 'getCategory', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function eventActions(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Events', 'getAction', $period, $date, $limit, $siteId);
    }

    /**
     * @return list<ReportRow>
     */
    public function eventNames(Period $period = Period::Day, string $date = 'today', ?int $limit = null, ?int $siteId = null): array
    {
        return $this->callReport('Events', 'getName', $period, $date, $limit, $siteId);
    }

    // =========================================================================
    // Goals
    // =========================================================================

    /**
     * @return array<int|string, mixed>
     */
    public function goals(Period $period = Period::Day, string $date = 'today', ?int $siteId = null): array
    {
        return $this->call('Goals', 'get', $period, $date, $siteId);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function goal(int $goalId, Period $period = Period::Day, string $date = 'today', ?int $siteId = null): array
    {
        $params = [
            'idSite' => $siteId ?? $this->defaultSiteId(),
            'period' => $period->value,
            'date' => $date,
            'idGoal' => $goalId,
        ];

        return $this->client->request('Goals.get', $params);
    }

    // =========================================================================
    // Live
    // =========================================================================

    public function liveCounters(int $lastMinutes = 30, ?int $siteId = null): LiveCounter
    {
        $params = [
            'idSite' => $siteId ?? $this->defaultSiteId(),
            'lastMinutes' => $lastMinutes,
        ];

        $response = $this->client->request('Live.getCounters', $params);

        return LiveCounter::fromArray($response[0] ?? []);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function liveVisitors(int $count = 10, ?int $siteId = null): array
    {
        $params = [
            'idSite' => $siteId ?? $this->defaultSiteId(),
            'period' => 'day',
            'date' => 'today',
            'filter_limit' => $count,
        ];

        $response = $this->client->request('Live.getLastVisitsDetails', $params);

        return array_values($response);
    }

    // =========================================================================
    // Generic Query
    // =========================================================================

    /**
     * @return array<int|string, mixed>
     */
    public function report(ReportQuery $query): array
    {
        return $this->client->request($query->toQueryParams()['method'], $query->toQueryParams());
    }

    public function query(string $module, string $method): ReportQuery
    {
        return ReportQuery::make($module, $method);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * @return array<int|string, mixed>
     */
    private function call(string $module, string $method, Period $period, string $date, ?int $siteId = null): array
    {
        $params = [
            'idSite' => $siteId ?? $this->defaultSiteId(),
            'period' => $period->value,
            'date' => $date,
        ];

        return $this->client->request("{$module}.{$method}", $params);
    }

    /**
     * @return list<ReportRow>
     */
    private function callReport(string $module, string $method, Period $period, string $date, ?int $limit, ?int $siteId = null): array
    {
        $params = [
            'idSite' => $siteId ?? $this->defaultSiteId(),
            'period' => $period->value,
            'date' => $date,
        ];

        if ($limit !== null) {
            $params['filter_limit'] = $limit;
        }

        $response = $this->client->request("{$module}.{$method}", $params);

        return array_values(array_map(fn (array $item) => ReportRow::fromArray($item), $response));
    }

    private function defaultSiteId(): int
    {
        return app(MatomoSettings::class)->site_id;
    }
}
