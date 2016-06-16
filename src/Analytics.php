<?php

namespace Spatie\Analytics;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;

class Analytics
{
    /** @var \Spatie\Analytics\AnalyticsClient */
    protected $client;

    /** @var string */
    protected $viewId;

    /**
     * @param AnalyticsClient $client
     * @param string          $viewId
     */
    public function __construct(AnalyticsClient $client, string $viewId)
    {
        $this->client = $client;

        $this->viewId = $viewId;
    }

    /**
     * Set the viewId.
     *
     * @param string $viewId
     *
     * @return $this
     */
    public function setViewId(string $viewId)
    {
        $this->viewId = $viewId;

        return $this;
    }

    public function getVisitorsAndPageViews(int $numberOfDays = 365, string $groupBy = 'date'): Collection
    {
        $period = Period::createForNumberOfDays($numberOfDays);

        return $this->getVisitorsAndPageViewsForPeriod($period->startDate, $period->endDate, $groupBy);
    }

    public function getVisitorsAndPageViewsForPeriod(DateTime $startDate, DateTime $endDate, string $groupBy = 'date'): Collection
    {
        $response = $this->performQuery($startDate, $endDate, 'ga:users,ga:pageviews', ['dimensions' => "ga:{$groupBy}"]);

        return collect($response['rows'] ?? [])->map(function (array $dateRow) use ($groupBy) {
            return [
                $groupBy => Carbon::createFromFormat(($groupBy == 'yearMonth' ? 'Ym' : 'Ymd'), $dateRow[0]),
                'visitors' => (int) $dateRow[1],
                'pageViews' => (int) $dateRow[2],
            ];
        });
    }

    /**
     * Get the top referrers.
     *
     * @param int $numberOfDays
     * @param int $maxResults
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTopReferrers($numberOfDays = 365, $maxResults = 20)
    {
        $period = Period::createForNumberOfDays($numberOfDays);

        return $this->getTopReferrersForPeriod($period->startDate, $period->endDate, $maxResults);
    }

    /**
     * Get the top referrers for the given period.
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int      $maxResults
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTopReferrersForPeriod(DateTime $startDate, DateTime $endDate, $maxResults)
    {
        $response = $this->performQuery($startDate, $endDate, 'ga:pageviews', ['dimensions' => 'ga:fullReferrer', 'sort' => '-ga:pageviews', 'max-results' => $maxResults]);

        if (is_null($response->rows)) {
            return new Collection([]);
        }

        return collect($response['rows'] ?? [])->map(function (array $pageRow) {
            return [
                'url' => $pageRow[0],
                'pageViews' => (int) $pageRow[1],
            ];
        });
    }

    /**
     * Get the top browsers.
     *
     * @param int $numberOfDays
     * @param int $maxResults
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTopBrowsers($numberOfDays = 365, $maxResults = 6)
    {
        $period = Period::createForNumberOfDays($numberOfDays);

        return $this->getTopBrowsersForPeriod($period->startDate, $period->endDate, $maxResults);
    }

    /**
     * Get the top browsers for the given period.
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int      $maxResults
     *
     * @return \Illuminate\Support\Collection
     */
    public function getTopBrowsersForPeriod(DateTime $startDate, DateTime $endDate, $maxResults)
    {
        $response = $this->performQuery($startDate, $endDate, 'ga:sessions', ['dimensions' => 'ga:browser', 'sort' => '-ga:sessions']);

        $topBrowsers = collect($response['rows'] ?? [])->map(function (array $browserRow) {
            return [
                'browser' => $browserRow[0],
                'sessions' => (int) $browserRow[1],
            ];
        });

        if ($topBrowsers->count() <= $maxResults) {
            return $topBrowsers;
        }

        $otherBrowsersRow = $topBrowsers
            ->splice($maxResults - 2)
            ->reduce(function (array $totals, array $browserRow) {

                $totals['sessions'] += (int) $browserRow['sessions'];

                return $totals;
            }, ['browser' => 'Others', 'sessions' => 0]);

        return $topBrowsers
            ->take($maxResults - 1)
            ->push($otherBrowsersRow);
    }

    /**
     * Get the most visited pages.
     *
     * @param int $numberOfDays
     * @param int $maxResults
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMostVisitedPages($numberOfDays = 365, $maxResults = 20)
    {
        $period = Period::createForNumberOfDays($numberOfDays);

        return $this->getMostVisitedPagesForPeriod($period->startDate, $period->endDate, $maxResults);
    }

    /**
     * Get the most visited pages for the given period.
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int      $maxResults
     *
     * @return \Illuminate\Support\Collection
     */
    public function getMostVisitedPagesForPeriod(DateTime $startDate, DateTime $endDate, $maxResults = 20)
    {
        $response = $this->performQuery($startDate, $endDate, 'ga:pageviews', ['dimensions' => 'ga:pagePath', 'sort' => '-ga:pageviews', 'max-results' => $maxResults]);

        return collect($response['rows'] ?? [])
            ->map(function (array $pageRow) {
                return [
                    'url' => $pageRow[0],
                    'pageViews' => (int) $pageRow[1],
                ];
            });
    }

    /**
     * Call the query method on the authenticated client.
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string   $metrics
     * @param array    $others
     *
     * @return array|null
     */
    public function performQuery(DateTime $startDate, DateTime $endDate, $metrics, $others = array())
    {
        return $this->client->performQuery(
            $this->viewId,
            $startDate,
            $endDate,
            $metrics,
            $others
        );
    }
}
