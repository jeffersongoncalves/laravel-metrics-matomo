<?php

namespace JeffersonGoncalves\MetricsMatomo\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JeffersonGoncalves\MetricsMatomo\MatomoServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelSettings\LaravelSettingsServiceProvider;
use Spatie\LaravelSettings\Migrations\SettingsMigrator;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
        $this->seedSettings();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelSettingsServiceProvider::class,
            MatomoServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUpDatabase(): void
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('group');
            $table->string('name');
            $table->boolean('locked')->default(false);
            $table->json('payload');
            $table->timestamps();
            $table->unique(['group', 'name']);
        });
    }

    protected function seedSettings(): void
    {
        $migrator = app(SettingsMigrator::class);

        $migrator->add('metrics-matomo.api_token', 'test-token');
        $migrator->add('metrics-matomo.site_id', 1);
        $migrator->add('metrics-matomo.base_url', 'https://matomo.example.com');
        $migrator->add('metrics-matomo.timezone', 'UTC');
    }
}
