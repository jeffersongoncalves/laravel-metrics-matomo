<?php

namespace JeffersonGoncalves\MetricsMatomo;

use Illuminate\Support\Facades\Config;
use JeffersonGoncalves\MetricsMatomo\Settings\MatomoSettings;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MatomoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('metrics-matomo');
    }

    public function packageRegistered(): void
    {
        Config::set('settings.settings', array_merge(
            Config::get('settings.settings', []),
            [MatomoSettings::class]
        ));

        $this->app->singleton(MatomoClient::class, function () {
            $settings = app(MatomoSettings::class);

            return new MatomoClient(
                token: $settings->api_token,
                baseUrl: $settings->base_url,
            );
        });

        $this->app->singleton('matomo', function ($app) {
            return new Matomo($app->make(MatomoClient::class));
        });

        $this->app->alias('matomo', Matomo::class);
    }

    public function packageBooted(): void
    {
        $migrationsPath = __DIR__.'/../database/settings';

        Config::set('settings.migrations_paths', array_merge(
            Config::get('settings.migrations_paths', []),
            [$migrationsPath]
        ));

        $this->publishes([
            $migrationsPath => database_path('settings'),
        ], 'metrics-matomo-settings-migrations');
    }
}
