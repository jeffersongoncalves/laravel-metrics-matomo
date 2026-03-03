<?php

use JeffersonGoncalves\MetricsMatomo\Enums\Period;
use JeffersonGoncalves\MetricsMatomo\Queries\ReportQuery;

it('creates a basic report query with default site id from settings', function () {
    $params = ReportQuery::make('VisitsSummary', 'get')
        ->toQueryParams();

    expect($params)
        ->toHaveKey('method', 'VisitsSummary.get')
        ->toHaveKey('idSite', 1)
        ->toHaveKey('period', 'day')
        ->toHaveKey('date', 'today');
});

it('supports custom site id', function () {
    $params = ReportQuery::make('VisitsSummary', 'get')
        ->site(5)
        ->toQueryParams();

    expect($params)->toHaveKey('idSite', 5);
});

it('supports period enum', function () {
    $params = ReportQuery::make('VisitsSummary', 'get')
        ->period(Period::Month)
        ->toQueryParams();

    expect($params)->toHaveKey('period', 'month');
});

it('supports string date', function () {
    $params = ReportQuery::make('VisitsSummary', 'get')
        ->date('yesterday')
        ->toQueryParams();

    expect($params)->toHaveKey('date', 'yesterday');
});

it('supports datetime objects', function () {
    $date = new DateTime('2026-03-01');

    $params = ReportQuery::make('VisitsSummary', 'get')
        ->date($date)
        ->toQueryParams();

    expect($params)->toHaveKey('date', '2026-03-01');
});

it('supports date range', function () {
    $params = ReportQuery::make('Actions', 'getPageUrls')
        ->dateRange('2026-01-01', '2026-01-31')
        ->toQueryParams();

    expect($params)
        ->toHaveKey('period', 'range')
        ->toHaveKey('date', '2026-01-01,2026-01-31');
});

it('supports date range with datetime objects', function () {
    $from = new DateTime('2026-02-01');
    $to = new DateTime('2026-02-28');

    $params = ReportQuery::make('Actions', 'getPageUrls')
        ->dateRange($from, $to)
        ->toQueryParams();

    expect($params)
        ->toHaveKey('period', 'range')
        ->toHaveKey('date', '2026-02-01,2026-02-28');
});

it('supports segment filter', function () {
    $params = ReportQuery::make('VisitsSummary', 'get')
        ->segment('referrerName==twitter.com')
        ->toQueryParams();

    expect($params)->toHaveKey('segment', 'referrerName==twitter.com');
});

it('supports limit', function () {
    $params = ReportQuery::make('Actions', 'getPageUrls')
        ->limit(50)
        ->toQueryParams();

    expect($params)->toHaveKey('filter_limit', 50);
});

it('supports sorting', function () {
    $params = ReportQuery::make('Actions', 'getPageUrls')
        ->sortBy('nb_visits', 'desc')
        ->toQueryParams();

    expect($params)
        ->toHaveKey('filter_sort_column', 'nb_visits')
        ->toHaveKey('filter_sort_order', 'desc');
});

it('supports language', function () {
    $params = ReportQuery::make('VisitsSummary', 'get')
        ->language('pt-br')
        ->toQueryParams();

    expect($params)->toHaveKey('language', 'pt-br');
});

it('supports expanded mode', function () {
    $params = ReportQuery::make('Actions', 'getPageUrls')
        ->expanded()
        ->toQueryParams();

    expect($params)->toHaveKey('expanded', 1);
});

it('supports flat mode', function () {
    $params = ReportQuery::make('Actions', 'getPageUrls')
        ->flat()
        ->toQueryParams();

    expect($params)->toHaveKey('flat', 1);
});

it('supports label filter', function () {
    $params = ReportQuery::make('Actions', 'getPageUrls')
        ->label('/blog')
        ->toQueryParams();

    expect($params)->toHaveKey('label', '/blog');
});

it('supports showColumns', function () {
    $params = ReportQuery::make('VisitsSummary', 'get')
        ->showColumns('nb_visits', 'nb_uniq_visitors')
        ->toQueryParams();

    expect($params)->toHaveKey('showColumns', 'nb_visits,nb_uniq_visitors');
});

it('supports hideColumns', function () {
    $params = ReportQuery::make('VisitsSummary', 'get')
        ->hideColumns('bounce_count', 'max_actions')
        ->toQueryParams();

    expect($params)->toHaveKey('hideColumns', 'bounce_count,max_actions');
});

it('supports extra params', function () {
    $params = ReportQuery::make('Goals', 'get')
        ->param('idGoal', 1)
        ->toQueryParams();

    expect($params)->toHaveKey('idGoal', 1);
});
