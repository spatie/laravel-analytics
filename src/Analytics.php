<?php

namespace Spatie\Analytics;

use Google\Analytics\Data\V1beta\FilterExpression;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

class Analytics
{
    use Macroable;

    public function __construct(
        protected AnalyticsClient $client,
        protected string $propertyId,
    ) {}

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
     * @return \Illuminate\Support\Collection<int, array{
     *   pageTitle: string,
     *   activeUsers: int,
     *   screenPageViews: int
     * }>
     */
    public function fetchVisitorsAndPageViews(Period $period, int $maxResults = 10, int $offset = 0): Collection
    {
        return $this->get(
            period: $period,
            metrics: ['activeUsers', 'screenPageViews'],
            dimensions: ['pageTitle'],
            maxResults: $maxResults,
            offset: $offset,
        );
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{
     *   pageTitle: string,
     *   date: \Carbon\Carbon,
     *   activeUsers: int,
     *   screenPageViews: int
     * }>
     */
    public function fetchVisitorsAndPageViewsByDate(Period $period, int $maxResults = 10, $offset = 0): Collection
    {
        return $this->get(
            period: $period,
            metrics: ['activeUsers', 'screenPageViews'],
            dimensions: ['pageTitle', 'date'],
            maxResults: $maxResults,
            orderBy: [
                OrderBy::dimension('date', true),
            ],
            offset: $offset,
        );
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{
     *   date: \Carbon\Carbon,
     *   activeUsers: int,
     *   screenPageViews: int
     * }>
     */
    public function fetchTotalVisitorsAndPageViews(Period $period, int $maxResults = 20, int $offset = 0): Collection
    {
        return $this->get(
            period: $period,
            metrics: ['activeUsers', 'screenPageViews'],
            dimensions: ['date'],
            maxResults: $maxResults,
            orderBy: [
                OrderBy::dimension('date', true),
            ],
            offset: $offset,
        );
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{
     *   pageTitle: string,
     *   fullPageUrl: string,
     *   screenPageViews: int
     * }>
     */
    public function fetchMostVisitedPages(Period $period, int $maxResults = 20, int $offset = 0): Collection
    {
        return $this->get(
            period: $period,
            metrics: ['screenPageViews'],
            dimensions: ['pageTitle', 'fullPageUrl'],
            maxResults: $maxResults,
            orderBy: [
                OrderBy::metric('screenPageViews', true),
            ],
            offset: $offset,
        );
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{
     *   pageReferrer: string,
     *   screenPageViews: int
     * }>
     */
    public function fetchTopReferrers(Period $period, int $maxResults = 20, int $offset = 0): Collection
    {
        return $this->get(
            period: $period,
            metrics: ['screenPageViews'],
            dimensions: ['pageReferrer'],
            maxResults: $maxResults,
            orderBy: [
                OrderBy::metric('screenPageViews', true),
            ],
            offset: $offset,
        );
    }

    /**
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
     * @return \Illuminate\Support\Collection<int, array{
     *   browser: string,
     *   screenPageViews: int
     * }>
     */
    public function fetchTopBrowsers(Period $period, int $maxResults = 10, int $offset = 0): Collection
    {
        return $this->get(
            period: $period,
            metrics: ['screenPageViews'],
            dimensions: ['browser'],
            maxResults: $maxResults,
            orderBy: [
                OrderBy::metric('screenPageViews', true),
            ],
            offset: $offset,
        );
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{
     *   country: string,
     *   screenPageViews: int
     * }>
     */
    public function fetchTopCountries(Period $period, int $maxResults = 10, int $offset = 0): Collection
    {
        return $this->get(
            period: $period,
            metrics: ['screenPageViews'],
            dimensions: ['country'],
            maxResults: $maxResults,
            orderBy: [
                OrderBy::metric('screenPageViews', true),
            ],
            offset: $offset,
        );
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{
     *   operatingSystem: string,
     *   screenPageViews: int
     * }>
     */
    public function fetchTopOperatingSystems(Period $period, int $maxResults = 10, int $offset = 0): Collection
    {
        return $this->get(
            period: $period,
            metrics: ['screenPageViews'],
            dimensions: ['operatingSystem'],
            maxResults: $maxResults,
            orderBy: [
                OrderBy::metric('screenPageViews', true),
            ],
            offset: $offset,
        );
    }

    public function get(
        Period $period,
        array $metrics,
        array $dimensions = [],
        int $maxResults = 10,
        array $orderBy = [],
        int $offset = 0,
        ?FilterExpression $dimensionFilter = null,
        bool $keepEmptyRows = false,
        ?FilterExpression $metricFilter = null,
    ): Collection {
        return $this->client->get(
            $this->propertyId,
            $period,
            $metrics,
            $dimensions,
            $maxResults,
            $orderBy,
            $offset,
            $dimensionFilter,
            $keepEmptyRows,
            $metricFilter
        );
    }

    public function getRealtime(
        Period $period,
        array $metrics,
        array $dimensions = [],
        int $maxResults = 10,
        array $orderBy = [],
        int $offset = 0,
        ?FilterExpression $dimensionFilter = null,
        bool $keepEmptyRows = false,
        ?FilterExpression $metricFilter = null,
    ): Collection {
        return $this->client->getRealtime(
            $this->propertyId,
            $period,
            $metrics,
            $dimensions,
            $maxResults,
            $orderBy,
            $offset,
            $dimensionFilter,
            $keepEmptyRows,
            $metricFilter
        );
    }
}
