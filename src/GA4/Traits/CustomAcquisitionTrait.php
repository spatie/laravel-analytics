<?php

namespace Botble\Analytics\GA4\Traits;

use Botble\Analytics\Period;
use Illuminate\Support\Arr;

trait CustomAcquisitionTrait
{
    public function getTotalUsers(Period $period): int
    {
        $result = $this->dateRange($period)
            ->metrics('totalUsers')
            ->get()
            ->table;

        return (int)Arr::first(Arr::flatten($result));
    }

    public function getTotalUsersByDate(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('date')
            ->orderByDimension('date')
            ->keepEmptyRows(true)
            ->get()
            ->table;
    }

    public function getTotalUsersBySessionSource(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('sessionSource')
            ->orderByMetricDesc('totalUsers')
            ->get()
            ->table;
    }

    public function getMostUsersByDate(Period $period, int $count = 20): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('date')
            ->orderByMetricDesc('totalUsers')
            ->limit($count)
            ->get()
            ->table;
    }

    public function getMostUsersBySessionSource(Period $period, int $count = 20): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('sessionSource')
            ->orderByMetricDesc('totalUsers')
            ->limit($count)
            ->get()
            ->table;
    }
}
