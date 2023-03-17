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
        // TODO: set correct metrics and dimensions.
        return $this->performQuery($period, ['activeUsers', 'newUsers'], ['pageTitle']);
    }

    public function performQuery(Period $period, array $metrics, array $dimensions = [], int $limit = 10): Collection
    {
        return $this->client->get($this->propertyId, $period, $metrics, $dimensions, $limit);
    }
}
