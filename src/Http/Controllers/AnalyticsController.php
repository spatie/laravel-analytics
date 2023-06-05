<?php

namespace Botble\Analytics\Http\Controllers;

use Botble\Analytics\Facades\Analytics;
use Botble\Analytics\Exceptions\InvalidConfiguration;
use Botble\Analytics\Period;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AnalyticsController extends BaseController
{
    public function getGeneral(Request $request, BaseHttpResponse $response)
    {
        $dashboardInstance = new DashboardWidgetInstance();
        $predefinedRangeFound = $dashboardInstance->getFilterRange($request->input('predefined_range'));
        if ($request->input('changed_predefined_range')) {
            $dashboardInstance->saveSettings(
                'widget_analytics_general',
                ['predefined_range' => $predefinedRangeFound['key']]
            );
        }

        $startDate = $predefinedRangeFound['startDate'];
        $endDate = $predefinedRangeFound['endDate'];
        $dimensions = $this->getDimension($predefinedRangeFound['key']);

        try {
            $period = Period::create($startDate, $endDate);

            $visitorData = [];

            $queryData = Analytics::performQuery($period, 'ga:visits,ga:pageviews', ['dimensions' => 'ga:' . $dimensions]);

            $queryRows = property_exists($queryData, 'rows') ? (array)$queryData->rows : $queryData->toArray();

            foreach ($queryRows as $dateRow) {
                $dateRow = array_values($dateRow);

                $visitorData[$dateRow[0]] = [
                    'axis' => $this->getAxisByDimensions($dateRow[0], $dimensions),
                    'visitors' => $dateRow[1],
                    'pageViews' => $dateRow[2],
                ];
            }

            if ($predefinedRangeFound['key'] == 'today') {
                for ($index = 0; $index < 24; $index++) {
                    if (! isset($visitorData[$index])) {
                        $visitorData[$index] = [
                            'axis' => $index . 'h',
                            'visitors' => 0,
                            'pageViews' => 0,
                        ];
                    }
                }
            }

            $stats = collect($visitorData);
            $countryStatsQuery = Analytics::performQuery(
                $period,
                'ga:sessions',
                ['dimensions' => 'ga:countryIsoCode']
            );

            $countryStats = property_exists($countryStatsQuery, 'rows') ? (array)$countryStatsQuery->rows : $countryStatsQuery->toArray();

            $metrics = 'ga:sessions, ga:users, ga:pageviews, ga:percentNewSessions, ga:bounceRate, ga:pageviewsPerVisit, ga:avgSessionDuration, ga:newUsers';

            $totalQuery = Analytics::performQuery($period, $metrics);

            $total = [];

            if (property_exists($totalQuery, 'totalsForAllResults')) {
                $total = $totalQuery->totalsForAllResults;
            } else {
                foreach (explode(', ', $metrics) as $metric) {
                    $total[$metric] = 0;
                }

                foreach ($totalQuery->toArray() as $item) {
                    $total['ga:sessions'] += $item['sessions'];
                    $total['ga:users'] += $item['totalUsers'];
                    $total['ga:pageviews'] += $item['screenPageViews'];
                    $total['ga:percentNewSessions'] += 0;
                    $total['ga:bounceRate'] += $item['bounceRate'];
                    $total['ga:pageviewsPerVisit'] += 0;
                    $total['ga:avgSessionDuration'] += 0;
                    $total['ga:newUsers'] += $item['newUsers'] ?? 0;
                }

                if ($totalQuery->count()) {
                    $total['ga:bounceRate'] = $total['ga:bounceRate'] / $totalQuery->count();
                }
            }

            foreach ($countryStats as $key => $item) {
                unset($item['countryIsoCode']);
                $countryStats[$key] = array_values($item);
            }

            return $response->setData(
                view(
                    'plugins/analytics::widgets.general',
                    compact('stats', 'countryStats', 'total')
                )->render()
            );
        } catch (InvalidConfiguration $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage() ?: trans('plugins/analytics::analytics.wrong_configuration'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    protected function getAxisByDimensions(string $dateRow, string $dimensions = 'hour'): string
    {
        return match ($dimensions) {
            'date' => Carbon::parse($dateRow)->toDateString(),
            'yearMonth' => Carbon::createFromFormat('Ym', $dateRow)->format('Y-m'),
            default => (int)$dateRow . 'h',
        };
    }

    protected function getDimension(string $key): string
    {
        $data = [
            'this_week' => 'date',
            'last_7_days' => 'date',
            'this_month' => 'date',
            'last_30_days' => 'date',
            'this_year' => 'yearMonth',
        ];

        return Arr::get($data, $key, 'hour');
    }

    public function getTopVisitPages(Request $request, BaseHttpResponse $response)
    {
        $dashboardInstance = new DashboardWidgetInstance();
        $predefinedRangeFound = $dashboardInstance->getFilterRange($request->input('predefined_range'));

        if ($request->input('changed_predefined_range')) {
            $dashboardInstance->saveSettings(
                'widget_analytics_page',
                ['predefined_range' => $predefinedRangeFound['key']]
            );
        }

        $startDate = $predefinedRangeFound['startDate'];
        $endDate = $predefinedRangeFound['endDate'];

        try {
            $period = Period::create($startDate, $endDate);
            $query = Analytics::fetchMostVisitedPages($period, 10);

            $pages = [];

            foreach ($query as $item) {
                $pages[] = [
                    'pageTitle' => $item['pageTitle'],
                    'url' => $item['fullPageUrl'] ?? $item['url'],
                    'pageViews' => $item['screenPageViews'] ?? $item['pageViews'],
                ];
            }

            return $response->setData(view('plugins/analytics::widgets.page', compact('pages'))->render());
        } catch (InvalidConfiguration $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage() ?: trans('plugins/analytics::analytics.wrong_configuration'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getTopBrowser(Request $request, BaseHttpResponse $response)
    {
        $dashboardInstance = new DashboardWidgetInstance();
        $predefinedRangeFound = $dashboardInstance->getFilterRange($request->input('predefined_range'));

        if ($request->input('changed_predefined_range')) {
            $dashboardInstance->saveSettings(
                'widget_analytics_browser',
                ['predefined_range' => $predefinedRangeFound['key']]
            );
        }

        $startDate = $predefinedRangeFound['startDate'];
        $endDate = $predefinedRangeFound['endDate'];

        try {
            $period = Period::create($startDate, $endDate);
            $browsers = Analytics::fetchTopBrowsers($period);

            return $response->setData(view('plugins/analytics::widgets.browser', compact('browsers'))->render());
        } catch (InvalidConfiguration $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage() ?: trans('plugins/analytics::analytics.wrong_configuration'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getTopReferrer(Request $request, BaseHttpResponse $response)
    {
        $dashboardInstance = new DashboardWidgetInstance();
        $predefinedRangeFound = $dashboardInstance->getFilterRange($request->input('predefined_range'));

        if ($request->input('changed_predefined_range')) {
            $dashboardInstance->saveSettings(
                'widget_analytics_referrer',
                ['predefined_range' => $predefinedRangeFound['key']]
            );
        }

        $startDate = $predefinedRangeFound['startDate'];
        $endDate = $predefinedRangeFound['endDate'];

        try {
            $period = Period::create($startDate, $endDate);
            $query = Analytics::fetchTopReferrers($period, 10);

            $referrers = [];

            foreach ($query as $item) {
                $referrers[] = [
                    'url' => $item['sessionSource'] ?? $item['url'],
                    'pageViews' => $item['screenPageViews'] ?? $item['pageViews'],
                ];
            }

            return $response->setData(view('plugins/analytics::widgets.referrer', compact('referrers'))->render());
        } catch (InvalidConfiguration $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage() ?: trans('plugins/analytics::analytics.wrong_configuration'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
