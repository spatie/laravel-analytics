<?php

namespace Spatie\Analytics;

use Illuminate\Support\Collection;
use Spatie\Macroable\Macroable;

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

    public function fetchVisitorsAndPageViews(Period $period, $maxResults = 10): Collection
    {
        return $this->get(
            $period,
            ['activeUsers', 'screenPageViews'],
            ['pageTitle'],
            $maxResults,
        );
    }

    public function fetchVisitorsAndPageViewsByDate(Period $period, $maxResults = 10): Collection
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

    public function fetchTotalVisitorsAndPageViews(Period $period, $maxResults = 20): Collection
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

    public function fetchMostVisitedPages(Period $period, $maxResults = 20): Collection
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

    public function fetchUserTypes(Period $period): Collection
    {
        return $this->get(
            $period,
            ['activeUsers'],
            ['newVsReturning'],
        );
    }

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

    public function get(Period $period, array $metrics, array $dimensions = [], int $maxResults = 10, array $orderBy = []): Collection
    {
        return $this->client->get($this->propertyId, $period, $metrics, $dimensions, $maxResults, $orderBy);
    }
}
