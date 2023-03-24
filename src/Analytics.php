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

    public function fetchVisitorsAndPageViews(Period $period): Collection
    {
        return $this->client->get(
            $this->propertyId,
            $period,
            ['activeUsers', 'screenPageViews'],
            ['pageTitle'],
        );
    }

    public function fetchVisitorsAndPageViewsByDate(Period $period, $limit = 10): Collection
    {
        return $this->client->get(
            $this->propertyId,
            $period,
            ['activeUsers', 'screenPageViews'],
            ['pageTitle', 'date'],
            $limit,
            [
                OrderBy::dimension('date', true),
            ],
        );
    }

    public function fetchTotalVisitorsAndPageViews(Period $period, $limit = 20): Collection
    {
        return $this->client->get(
            $this->propertyId,
            $period,
            ['activeUsers', 'screenPageViews'],
            ['date'],
            $limit,
            [
                OrderBy::dimension('date', true),
            ],
        );
    }

    public function fetchMostVisitedPages(Period $period, $maxResults = 20): Collection
    {
        ray('fetchMostVisitedPages', $period, $maxResults);
        return $this->client->get(
            $this->propertyId,
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
        return $this->client->get(
            $this->propertyId,
            $period,
            ['screenPageViews'],
            ['pageReferrer'],
            $maxResults,
            ['screenPageViews'],
        );
    }

    public function fetchUserTypes(Period $period): Collection
    {
        return $this->client->get(
            $this->propertyId,
            $period,
            ['activeUsers'],
            ['newVsReturning'],
        );
    }

    public function fetchTopBrowsers(Period $period, int $maxResults = 10): Collection
    {
        return $this->client->get(
            $this->propertyId,
            $period,
            ['screenPageViews'],
            ['browser'],
            $maxResults,
            ['screenPageViews'],
        );
    }
}
