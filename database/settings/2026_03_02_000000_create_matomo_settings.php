<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('metrics-matomo', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('api_token', env('MATOMO_API_TOKEN', ''));
            $blueprint->add('site_id', (int) env('MATOMO_SITE_ID', 1));
            $blueprint->add('base_url', env('MATOMO_BASE_URL', ''));
            $blueprint->add('timezone', env('MATOMO_TIMEZONE', 'UTC'));
        });
    }
};
