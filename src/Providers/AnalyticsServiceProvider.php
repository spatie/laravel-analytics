<?php

namespace Botble\Analytics\Providers;

use Botble\Analytics\Analytics;
use Botble\Analytics\AnalyticsClient;
use Botble\Analytics\AnalyticsClientFactory;
use Botble\Analytics\Abstracts\AnalyticsAbstract;
use Botble\Analytics\Facades\Analytics as AnalyticsFacade;
use Botble\Analytics\GA4\Analytics as AnalyticsGA4;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Botble\Analytics\Exceptions\InvalidConfiguration;

class AnalyticsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        if (! class_exists('Google\Service\Analytics\GaData')) {
            return;
        }

        $this->app->bind(AnalyticsClient::class, function () {
            return AnalyticsClientFactory::createForConfig(config('plugins.analytics.general'));
        });

        $this->app->bind(AnalyticsAbstract::class, function () {
            $credentials = setting('analytics_service_account_credentials');

            if (! $credentials) {
                throw InvalidConfiguration::credentialsIsNotValid();
            }

            if ($propertyId = setting('analytics_property_id')) {
                if (! is_numeric($propertyId)) {
                    throw InvalidConfiguration::invalidPropertyId();
                }

                return new AnalyticsGA4($propertyId, $credentials);
            }

            $viewId = setting('analytics_view_id');

            if (empty($viewId)) {
                throw InvalidConfiguration::propertyIdNotSpecified();
            }

            return new Analytics($this->app->make(AnalyticsClient::class), $viewId);
        });

        AliasLoader::getInstance()->alias('Analytics', AnalyticsFacade::class);
    }

    public function boot(): void
    {
        $this->setNamespace('plugins/analytics')
            ->loadAndPublishConfigurations(['general', 'permissions'])
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishAssets();

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
