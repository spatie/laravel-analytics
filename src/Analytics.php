<?php

namespace Botble\Analytics;

use Botble\Analytics\Abstracts\AnalyticsAbstract;
use Botble\Analytics\Abstracts\AnalyticsContract;
use Google\Service\Analytics\GaData;
use Google_Service_Analytics;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Analytics extends AnalyticsAbstract implements AnalyticsContract
{
    public function __construct(protected AnalyticsClient $client, public string|null $propertyId)
    {
    }

    /**
     * Call the query method on the authenticated client.
     */
    public function performQuery(Period $period, string $metrics, array $others = []): Collection|array|GaData|null
    {
        return $this->client->performQuery(
            $this->propertyId,
            $period->startDate,
            $period->endDate,
            $metrics,
            $others
        );
    }

    public function fetchMostVisitedPages(Period $period, int $maxResults = 20): Collection
    {
        $response = $this->performQuery(
            $period,
            'ga:pageviews',
            [
                'dimensions' => 'ga:pagePath,ga:pageTitle',
                'sort' => '-ga:pageviews',
                'max-results' => $maxResults,
            ]
        );

        return collect($response['rows'] ?? [])
            ->map(function (array $pageRow) {
                return [
                    'url' => $pageRow[0],
                    'pageTitle' => $pageRow[1],
                    'pageViews' => (int)$pageRow[2],
                ];
            });
    }

    public function fetchTopReferrers(Period $period, int $maxResults = 20): Collection
    {
        $response = $this->performQuery(
            $period,
            'ga:pageviews',
            [
                'dimensions' => 'ga:fullReferrer',
                'sort' => '-ga:pageviews',
                'max-results' => $maxResults,
            ]
        );

        return collect($response['rows'] ?? [])->map(function (array $pageRow) {
            return [
                'url' => $pageRow[0],
                'pageViews' => (int)$pageRow[1],
            ];
        });
    }

    public function fetchUserTypes(Period $period): Collection
    {
        $response = $this->performQuery(
            $period,
            'ga:sessions',
            [
                'dimensions' => 'ga:userType',
            ]
        );

        $data = Arr::map($response->rows ?? [], function (array $userRow) {
            return [
                'type' => $userRow[0],
                'sessions' => (int)$userRow[1],
            ];
        });

        return collect($data);
    }

    public function fetchTopBrowsers(Period $period, int $maxResults = 10): Collection
    {
        $response = $this->performQuery(
            $period,
            'ga:sessions',
            [
                'dimensions' => 'ga:browser',
                'sort' => '-ga:sessions',
            ]
        );

        $topBrowsers = collect($response['rows'] ?? [])->map(function (array $browserRow) {
            return [
                'browser' => $browserRow[0],
                'sessions' => (int)$browserRow[1],
            ];
        });

        if ($topBrowsers->count() <= $maxResults) {
            return $topBrowsers;
        }

        return $this->summarizeTopBrowsers($topBrowsers, $maxResults);
    }

    protected function summarizeTopBrowsers(Collection $topBrowsers, int $maxResults): Collection
    {
        return $topBrowsers
            ->take($maxResults - 1)
            ->push([
                'browser' => 'Others',
                'sessions' => $topBrowsers->splice($maxResults - 1)->sum('sessions'),
            ]);
    }

    /*
     * Get the underlying Google_Service_Analytics object. You can use this
     * to basically call anything on the Google Analytics API.
     */
    public function getAnalyticsService(): Google_Service_Analytics
    {
        return $this->client->getAnalyticsService();
    }
}
