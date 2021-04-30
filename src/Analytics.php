<?php

namespace Spatie\Analytics;

use Carbon\Carbon;
use Google_Service_Analytics;
use Google_Service_Analytics_GaData;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class Analytics
{
    use Macroable;

    public function __construct(
        protected AnalyticsClient $client,
        protected string $viewId,
    ) {
    }

    public function setViewId(string $viewId): self
    {
        $this->viewId = $viewId;

        return $this;
    }

    public function getViewId()
    {
        return $this->viewId;
    }

    public function fetchVisitorsAndPageViews(Period $period): Collection
    {
        $response = $this->performQuery(
            $period,
            'ga:users,ga:pageviews',
            ['dimensions' => 'ga:date,ga:pageTitle'],
        );

        return collect($response['rows'] ?? [])->map(fn (array $dateRow) => [
            'date' => Carbon::createFromFormat('Ymd', $dateRow[0]),
            'pageTitle' => $dateRow[1],
            'visitors' => (int) $dateRow[2],
            'pageViews' => (int) $dateRow[3],
        ]);
    }

    public function fetchTotalVisitorsAndPageViews(Period $period): Collection
    {
        $response = $this->performQuery(
            $period,
            'ga:users,ga:pageviews',
            ['dimensions' => 'ga:date'],
        );

        return collect($response['rows'] ?? [])->map(fn (array $dateRow) => [
            'date' => Carbon::createFromFormat('Ymd', $dateRow[0]),
            'visitors' => (int) $dateRow[1],
            'pageViews' => (int) $dateRow[2],
        ]);
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
            ],
        );

        return collect($response['rows'] ?? [])->map(fn (array $pageRow) => [
            'url' => $pageRow[0],
            'pageTitle' => $pageRow[1],
            'pageViews' => (int) $pageRow[2],
        ]);
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
            ],
        );

        return collect($response['rows'] ?? [])->map(fn (array $pageRow) => [
            'url' => $pageRow[0],
            'pageViews' => (int) $pageRow[1],
        ]);
    }

    public function fetchUserTypes(Period $period): Collection
    {
        $response = $this->performQuery(
            $period,
            'ga:sessions',
            [
                'dimensions' => 'ga:userType',
            ],
        );

        return collect($response->rows ?? [])->map(fn (array $userRow) => [
            'type' => $userRow[0],
            'sessions' => (int) $userRow[1],
        ]);
    }

    public function fetchTopBrowsers(Period $period, int $maxResults = 10): Collection
    {
        $response = $this->performQuery(
            $period,
            'ga:sessions',
            [
                'dimensions' => 'ga:browser',
                'sort' => '-ga:sessions',
            ],
        );

        $topBrowsers = collect($response['rows'] ?? [])->map(fn (array $browserRow) => [
            'browser' => $browserRow[0],
            'sessions' => (int) $browserRow[1],
        ]);

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

    /**
     * Call the query method on the authenticated client.
     *
     * @param Period $period
     * @param string $metrics
     * @param array  $others
     *
     * @return Google_Service_Analytics_GaData|array|null
     */
    public function performQuery(Period $period, string $metrics, array $others = []): Google_Service_Analytics_GaData | array | null
    {
        return $this->client->performQuery(
            $this->viewId,
            $period->startDate,
            $period->endDate,
            $metrics,
            $others,
        );
    }

    /**
     * Get the underlying Google_Service_Analytics object. You can use this
     * to basically call anything on the Google Analytics API.
     */
    public function getAnalyticsService(): Google_Service_Analytics
    {
        return $this->client->getAnalyticsService();
    }
}
