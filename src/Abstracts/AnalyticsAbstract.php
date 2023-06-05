<?php

namespace Botble\Analytics\Abstracts;

use Botble\Analytics\Period;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

abstract class AnalyticsAbstract
{
    use Macroable;

    public string|null $propertyId = null;

    public string|null $credentials = null;

    public function getPropertyId(): string
    {
        return $this->propertyId;
    }

    public function setPropertyId(string $propertyId): static
    {
        $this->propertyId = $propertyId;

        return $this;
    }

    abstract public function fetchMostVisitedPages(Period $period, int $maxResults = 20): Collection;

    abstract public function fetchTopReferrers(Period $period, int $maxResults = 20): Collection;

    abstract public function fetchTopBrowsers(Period $period, int $maxResults = 10): Collection;
}
