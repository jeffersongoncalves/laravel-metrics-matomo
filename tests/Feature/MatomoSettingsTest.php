<?php

use JeffersonGoncalves\MetricsMatomo\Settings\MatomoSettings;

it('resolves settings from container', function () {
    $settings = app(MatomoSettings::class);

    expect($settings)->toBeInstanceOf(MatomoSettings::class);
});

it('has correct default values from seed', function () {
    $settings = app(MatomoSettings::class);

    expect($settings->api_token)->toBe('test-token')
        ->and($settings->site_id)->toBe(1)
        ->and($settings->base_url)->toBe('https://matomo.example.com')
        ->and($settings->timezone)->toBe('UTC');
});

it('can update and persist settings', function () {
    $settings = app(MatomoSettings::class);
    $settings->site_id = 5;
    $settings->timezone = 'America/Sao_Paulo';
    $settings->save();

    $fresh = app(MatomoSettings::class);

    expect($fresh->site_id)->toBe(5)
        ->and($fresh->timezone)->toBe('America/Sao_Paulo');
});
