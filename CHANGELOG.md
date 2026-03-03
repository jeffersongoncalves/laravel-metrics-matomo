# Changelog

All notable changes to `laravel-metrics-matomo` will be documented in this file.

## v1.0.1 - 2026-03-02

### Fix

- Fix PHPStan level 8 type errors on return types (`array<int|string, mixed>` for API responses)

## v1.0.0 - 2026-03-02

### Initial Release

#### Features

- **Matomo Reporting API** integration via HTTP client with `token_auth` authentication
  
- **Settings via database** using `spatie/laravel-settings` — no config files needed
  
- **30+ API methods** covering all major Matomo reporting modules:
  
  - **VisitsSummary**: visits, unique visitors, actions, bounce rate, avg time on site
  - **Actions**: page URLs, page titles, entry/exit pages, downloads, outlinks, site search
  - **Referrers**: referrer types, search engines, keywords, websites, socials, campaigns
  - **UserCountry**: countries, cities, regions, continents
  - **DevicesDetection**: device types, browsers, OS families
  - **Resolution**: screen resolutions
  - **Events**: categories, actions, names
  - **Goals**: all goals, specific goal
  - **Live**: real-time counters, last visitors details
  
- **Fluent ReportQuery builder** for custom reports with support for:
  
  - Period (day, week, month, year, range)
  - Date ranges with DateTime objects
  - Segments, sorting, limits
  - Column filtering (show/hide)
  - Expanded and flat modes
  - Extra parameters
  
- **Data classes**: `VisitSummary`, `LiveCounter`, `ReportRow`
  
- **Facade** with full PHPDoc annotations for IDE autocompletion
  
- **Full test suite**: 34 tests, 88 assertions
  
- **PHPStan level 8** static analysis
  
- **Laravel Pint** code styling
  

## Unreleased
