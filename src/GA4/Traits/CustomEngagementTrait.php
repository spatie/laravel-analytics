<?php

namespace Botble\Analytics\GA4\Traits;

use Botble\Analytics\Period;
use Illuminate\Support\Arr;

trait CustomEngagementTrait
{
    public function getAverageSessionDuration(Period $period): float
    {
        $result = $this->dateRange($period)
            ->metrics('averageSessionDuration')
            ->get()
            ->table;

        return (float)Arr::first(Arr::flatten($result));
    }

    public function getAverageSessionDurationByDate(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('averageSessionDuration')
            ->dimensions('date')
            ->orderByDimension('date')
            ->keepEmptyRows(true)
            ->get()
            ->table;
    }

    public function getTotalViews(Period $period): int
    {
        $result = $this->dateRange($period)
            ->metrics('screenPageViews')
            ->get()
            ->table;

        return (int)Arr::first(Arr::flatten($result));
    }

    public function getTotalViewsByDate(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('screenPageViews')
            ->dimensions('date')
            ->orderByDimension('date')
            ->keepEmptyRows(true)
            ->get()
            ->table;
    }

    public function getTotalViewsByPage(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('screenPageViews')
            ->dimensions('pageTitle', 'fullPageUrl')
            ->get()
            ->table;
    }

    public function getTotalViewsByPageAndUser(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('screenPageViews', 'totalUsers')
            ->dimensions('pageTitle', 'fullPageUrl')
            ->get()
            ->table;
    }

    public function getMostViewsByPage(Period $period, int $count = 20): array
    {
        return $this->dateRange($period)
            ->metrics('screenPageViews')
            ->dimensions('pageTitle', 'fullPageUrl')
            ->orderByMetricDesc('screenPageViews')
            ->limit($count)
            ->get()
            ->table;
    }

    public function getMostViewsByUser(Period $period, int $count = 20): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('pageTitle', 'fullPageUrl')
            ->orderByMetricDesc('totalUsers')
            ->limit($count)
            ->get()
            ->table;
    }
}
