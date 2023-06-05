<?php

namespace Botble\Analytics;

use Botble\Dashboard\Models\DashboardWidget;
use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Facades\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        $widgets = app(DashboardWidgetInterface::class)
            ->advancedGet([
                'condition' => [
                    [
                        'name',
                        'IN',
                        [
                            'widget_analytics_general',
                            'widget_analytics_page',
                            'widget_analytics_browser',
                            'widget_analytics_referrer',
                        ],
                    ],
                ],
            ]);

        foreach ($widgets as $widget) {
            /**
             * @var DashboardWidget $widget
             */
            $widget->delete();
        }

        Setting::delete([
            'google_analytics',
            'analytics_property_id',
            'analytics_service_account_credentials',
        ]);
    }
}
