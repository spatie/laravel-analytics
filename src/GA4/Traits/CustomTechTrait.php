<?php

namespace Botble\Analytics\GA4\Traits;

use Botble\Analytics\Period;

trait CustomTechTrait
{
    public function getTotalUsersByPlatform(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('platform')
            ->orderByMetricDesc('totalUsers')
            ->get()
            ->table;
    }

    public function getTotalUsersByOperatingSystem(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('operatingSystem')
            ->orderByMetricDesc('totalUsers')
            ->get()
            ->table;
    }

    public function getTotalUsersByBrowser(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('browser')
            ->orderByMetricDesc('totalUsers')
            ->get()
            ->table;
    }

    public function getTotalUsersByScreenResolution(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('screenResolution')
            ->orderByMetricDesc('totalUsers')
            ->get()
            ->table;
    }

    public function getMostUsersByPlatform(Period $period, int $count = 20): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('platform')
            ->orderByMetricDesc('totalUsers')
            ->limit($count)
            ->get()
            ->table;
    }

    public function getMostUsersByOperatingSystem(Period $period, int $count = 20): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('operatingSystem')
            ->orderByMetricDesc('totalUsers')
            ->limit($count)
            ->get()
            ->table;
    }

    public function getMostUsersByBrowser(Period $period, int $count = 20): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('browser')
            ->orderByMetricDesc('totalUsers')
            ->limit($count)
            ->get()
            ->table;
    }

    public function getMostUsersByScreenResolution(Period $period, int $count = 20): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('screenResolution')
            ->orderByMetricDesc('totalUsers')
            ->limit($count)
            ->get()
            ->table;
    }
}
