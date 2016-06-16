<?php

namespace Spatie\Analytics;

use Google_Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Spatie\Analytics\Exceptions\InvalidConfiguration;
use Spatie\Analytics\LaravelAnalytics;

class LaravelAnalyticsServiceProvider extends ServiceProvider
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
        
        $this->guardAgainstInvalidConfiguration($analyticsConfig);
        
        $this->app->bind(LaravelAnalytics::class, function () use ($analyticsConfig) {
            
            return AnalyticsFactory::createForConfig($analyticsConfig);
            
        });

        $this->app->alias(LaravelAnalytics::class, 'laravel-analytics');
    }

   

    protected function guardAgainstInvalidConfiguration(array $analyticsConfig)
    {
        if (empty($analyticsConfig['site_id'])) {
            throw InvalidConfiguration::siteIdNotSpecified();
        }

        if (! starts_with($analyticsConfig['site_id'], 'ga:')) {
            throw InvalidConfiguration::siteIdNotValid($analyticsConfig['site_id']);
        }
        
        if (! file_exists($analyticsConfig['client_secret_json'])) {
            throw InvalidConfiguration::clientSecretJsonFileDoesNotExist();
        }
    }
}
