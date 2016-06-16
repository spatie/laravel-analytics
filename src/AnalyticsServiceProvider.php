<?php

namespace Spatie\Analytics;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Spatie\Analytics\Exceptions\InvalidConfiguration;

class AnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laravel-analytics.php' => config_path('laravel-analytics.php'),
        ]);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $analyticsConfig = config('laravel-analytics');

        $this->app->bind(LaravelAnalytics::class, function () use ($analyticsConfig) {

            $this->guardAgainstInvalidConfiguration($analyticsConfig);

            return AnalyticsFactory::createForConfig($analyticsConfig);

        });

        $this->app->alias(LaravelAnalytics::class, 'laravel-analytics');
    }

    protected function guardAgainstInvalidConfiguration(array $analyticsConfig)
    {
        if (empty($analyticsConfig['view_id'])) {
            throw InvalidConfiguration::viewIdNotSpecified();
        }

        if (!file_exists($analyticsConfig['client_secret_json'])) {
            throw InvalidConfiguration::clientSecretJsonFileDoesNotExist();
        }
    }
}
