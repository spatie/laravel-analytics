<?php

namespace Spatie\Analytics;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class Analytics
{
    use Macroable;

    public function __construct(
        protected AnalyticsClient $client,
        protected string $propertyId,
    ) {
    }

    public function setPropertyId(string $propertyId): self
    {
        $this->propertyId = $propertyId;

        return $this;
    }

    public function getPropertyId(): string
    {
        return $this->propertyId;
    }

    /**
     * @param  \Spatie\Analytics\Period  $period
     * @return \Illuminate\Support\Collection<int, array{
     *   pageTitle: string,
     *   activeUsers: int,
     *   screenPageViews: int
     * }>
     */
    public function fetchVisitorsAndPageViews(Period $period, int $maxResults = 10): Collection
    {
        return $this->get(
            $period,
            ['activeUsers', 'screenPageViews'],
            ['pageTitle'],
            $maxResults,
        );
    }

    /**
     * @param  \Spatie\Analytics\Period  $period
     * @return \Illuminate\Support\Collection<int, array{
     *   pageTitle: string,
     *   date: \Carbon\Carbon,
     *   activeUsers: int,
     *   screenPageViews: int
     * }>
     */
    public function fetchVisitorsAndPageViewsByDate(Period $period, int $maxResults = 10): Collection
    {
        return $this->get(
            $period,
            ['activeUsers', 'screenPageViews'],
            ['pageTitle', 'date'],
            $maxResults,
            [
                OrderBy::dimension('date', true),
            ],
        );
    }

    /**
     * @param  \Spatie\Analytics\Period  $period
     * @return \Illuminate\Support\Collection<int, array{
     *   date: \Carbon\Carbon,
     *   activeUsers: int,
     *   screenPageViews: int
     * }>
     */
    public function fetchTotalVisitorsAndPageViews(Period $period, int $maxResults = 20): Collection
    {
        return $this->get(
            $period,
            ['activeUsers', 'screenPageViews'],
            ['date'],
            $maxResults,
            [
                OrderBy::dimension('date', true),
            ],
        );
    }

    /**
     * @param  \Spatie\Analytics\Period  $period
     * @return \Illuminate\Support\Collection<int, array{
     *   pageTitle: string,
     *   fullPageUrl: string,
     *   screenPageViews: int
     * }>
     */
    public function fetchMostVisitedPages(Period $period, int $maxResults = 20): Collection
    {
        return $this->get(
            $period,
            ['screenPageViews'],
            ['pageTitle', 'fullPageUrl'],
            $maxResults,
            [
                OrderBy::metric('screenPageViews', true),
            ],
        );
    }

    /**
     * @param  \Spatie\Analytics\Period  $period
     * @return \Illuminate\Support\Collection<int, array{
     *   pageReferrer: string,
     *   screenPageViews: int
     * }>
     */
    public function fetchTopReferrers(Period $period, int $maxResults = 20): Collection
    {
        return $this->get(
            $period,
            ['screenPageViews'],
            ['pageReferrer'],
            $maxResults,
            [
                OrderBy::metric('screenPageViews', true),
            ],
        );
    }

    /**
     * @param  \Spatie\Analytics\Period  $period
     * @return \Illuminate\Support\Collection<int, array{
     *   newVsReturning: string,
     *   activeUsers: int
     * }>
     */
    public function fetchUserTypes(Period $period): Collection
    {
        return $this->get(
            $period,
            ['activeUsers'],
            ['newVsReturning'],
        );
    }

    /**
     * @param  \Spatie\Analytics\Period  $period
     * @return \Illuminate\Support\Collection<int, array{
     *   browser: string,
     *   screenPageViews: int
     * }>
     */
    public function fetchTopBrowsers(Period $period, int $maxResults = 10): Collection
    {
        return $this->get(
            $period,
            ['screenPageViews'],
            ['browser'],
            $maxResults,
            [
                OrderBy::metric('screenPageViews', true),
            ],
        );
    }

    public function get(
        Period $period,
        array $metrics,
        array $dimensions = [],
        int $maxResults = 10,
        array $orderBy = [],
    ): Collection {
        return $this->client->get(
            $this->propertyId,
            $period,
            $metrics,
            $dimensions,
            $maxResults,
            $orderBy,
        );
    }
}
