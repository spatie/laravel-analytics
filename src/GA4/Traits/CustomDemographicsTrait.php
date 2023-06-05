<?php

namespace Botble\Analytics\GA4\Traits;

use Botble\Analytics\Period;

trait CustomDemographicsTrait
{
    public function getTotalUsersByCountry(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('country', 'countryId')
            ->orderByMetricDesc('totalUsers')
            ->get()
            ->table;
    }

    public function getTotalUsersByCity(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('city', 'cityId')
            ->orderByMetricDesc('totalUsers')
            ->get()
            ->table;
    }

    public function getTotalUsersByGender(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('userGender')
            ->orderByMetricDesc('totalUsers')
            ->get()
            ->table;
    }

    public function getTotalUsersByLanguage(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('language')
            ->orderByMetricDesc('totalUsers')
            ->get()
            ->table;
    }

    public function getTotalUsersByAge(Period $period): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('userAgeBracket')
            ->orderByMetricDesc('totalUsers')
            ->get()
            ->table;
    }

    public function getMostUsersByCountry(Period $period, int $count = 20): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('country', 'countryId')
            ->orderByMetricDesc('totalUsers')
            ->limit($count)
            ->get()
            ->table;
    }

    public function getMostUsersByCity(Period $period, int $count = 20): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('city', 'cityId')
            ->orderByMetricDesc('totalUsers')
            ->limit($count)
            ->get()
            ->table;
    }

    public function getMostUsersByLanguage(Period $period, int $count = 20): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('language')
            ->orderByMetricDesc('totalUsers')
            ->limit($count)
            ->get()
            ->table;
    }

    public function getMostUsersByAge(Period $period, int $count = 20): array
    {
        return $this->dateRange($period)
            ->metrics('totalUsers')
            ->dimensions('userAgeBracket')
            ->orderByMetricDesc('totalUsers')
            ->limit($count)
            ->get()
            ->table;
    }
}
