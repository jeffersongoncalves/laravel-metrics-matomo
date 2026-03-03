<?php

namespace JeffersonGoncalves\MetricsMatomo\Settings;

use Spatie\LaravelSettings\Settings;

class MatomoSettings extends Settings
{
    public string $api_token;

    public int $site_id;

    public string $base_url;

    public string $timezone;

    public static function group(): string
    {
        return 'metrics-matomo';
    }
}
