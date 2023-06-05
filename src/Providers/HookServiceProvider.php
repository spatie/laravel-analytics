<?php

namespace Botble\Analytics\Providers;

use Botble\Base\Facades\Assets;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! config('plugins.analytics.general.enabled_dashboard_widgets')) {
            return;
        }

        add_action(DASHBOARD_ACTION_REGISTER_SCRIPTS, [$this, 'registerScripts'], 18);
        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'addAnalyticsWidgets'], 18, 2);
        add_filter(BASE_FILTER_AFTER_SETTING_CONTENT, [$this, 'addAnalyticsSetting'], 99);
        add_filter('cms_settings_validation_rules', [$this, 'addAnalyticsSettingRules'], 99);
        add_filter('core_layout_before_content', [$this, 'showMissingLibraryWarning'], 99);
    }

    public function registerScripts(): void
    {
        if (Auth::user()->hasAnyPermission([
            'analytics.general',
            'analytics.page',
            'analytics.browser',
            'analytics.referrer',
        ])) {
            Assets::addScripts(['raphael', 'morris'])
                ->addStyles(['morris'])
                ->addStylesDirectly([
                    'vendor/core/plugins/analytics/libraries/jvectormap/jquery-jvectormap-1.2.2.css',
                ])
                ->addScriptsDirectly([
                    'vendor/core/plugins/analytics/libraries/jvectormap/jquery-jvectormap-1.2.2.min.js',
                    'vendor/core/plugins/analytics/libraries/jvectormap/jquery-jvectormap-world-mill-en.js',
                    'vendor/core/plugins/analytics/js/analytics.js',
                ]);
        }
    }

    public function addAnalyticsWidgets(array $widgets, Collection $widgetSettings): array
    {
        $dashboardWidgetInstance = new DashboardWidgetInstance();

        $widgets = $dashboardWidgetInstance
            ->setPermission('analytics.general')
            ->setKey('widget_analytics_general')
            ->setTitle(trans('plugins/analytics::analytics.widget_analytics_general'))
            ->setIcon('fas fa-chart-line')
            ->setColor('#f2784b')
            ->setRoute(route('analytics.general'))
            ->setBodyClass('row')
            ->setHasLoadCallback(true)
            ->setIsEqualHeight(false)
            ->setSettings(['show_predefined_ranges' => true])
            ->init($widgets, $widgetSettings);

        $widgets = $dashboardWidgetInstance
            ->setPermission('analytics.page')
            ->setKey('widget_analytics_page')
            ->setTitle(trans('plugins/analytics::analytics.widget_analytics_page'))
            ->setIcon('far fa-newspaper')
            ->setColor('#3598dc')
            ->setRoute(route('analytics.page'))
            ->setBodyClass('scroll-table')
            ->setColumn('col-md-6 col-sm-6')
            ->setSettings(['show_predefined_ranges' => true])
            ->init($widgets, $widgetSettings);

        $widgets = $dashboardWidgetInstance
            ->setPermission('analytics.browser')
            ->setKey('widget_analytics_browser')
            ->setTitle(trans('plugins/analytics::analytics.widget_analytics_browser'))
            ->setIcon('fab fa-safari')
            ->setColor('#8e44ad')
            ->setRoute(route('analytics.browser'))
            ->setBodyClass('scroll-table')
            ->setColumn('col-md-6 col-sm-6')
            ->setSettings(['show_predefined_ranges' => true])
            ->init($widgets, $widgetSettings);

        return $dashboardWidgetInstance
            ->setPermission('analytics.referrer')
            ->setKey('widget_analytics_referrer')
            ->setTitle(trans('plugins/analytics::analytics.widget_analytics_referrer'))
            ->setIcon('fas fa-user-friends')
            ->setColor('#3598dc')
            ->setRoute(route('analytics.referrer'))
            ->setBodyClass('scroll-table')
            ->setColumn('col-md-6 col-sm-6')
            ->setSettings(['show_predefined_ranges' => true])
            ->init($widgets, $widgetSettings);
    }

    public function addAnalyticsSetting(string|null $data = null): string
    {
        return $data . view('plugins/analytics::setting')->render();
    }

    public function addAnalyticsSettingRules(array $rules): array
    {
        $rules['google_analytics'] = 'nullable|string|starts_with:G-';
        $rules['analytics_property_id'] = 'nullable|string|min:9|max:9';
        $rules['analytics_service_account_credentials'] = 'nullable|json';

        return $rules;
    }

    public function showMissingLibraryWarning(string|null $html): string|null
    {
        if (! Route::is('plugins.index') || class_exists('Google\Service\Analytics\GaData')) {
            return $html;
        }

        return $html . view('plugins/analytics::missing-library-warning')->render();
    }
}
