<?php

namespace Spatie\Analytics;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AnalyticsClient
{
    protected int $cacheLifeTimeInMinutes = 0;

    public function __construct(
        protected BetaAnalyticsDataClient $service,
        protected Repository $cache,
    ) {
        //
    }

    public function setCacheLifeTimeInMinutes(int $cacheLifeTimeInMinutes): self
    {
        $this->cacheLifeTimeInMinutes = $cacheLifeTimeInMinutes * 60;

        return $this;
    }

    /**
     * @param  array<string>  $metrics
     * @param  array<string>  $dimensions
     */
    public function get(
        string $propertyId,
        Period $period,
        array $metrics,
        array $dimensions = [],
        int $limit = 10,
        array $orderBy = [],
    ): Collection {
        $response = $this->runReport([
            'property' => "properties/{$propertyId}",
            'dateRanges' => [
                $period->toDateRange(),
            ],
            'metrics' => $this->getFormattedMetrics($metrics),
            'dimensions' => $this->getFormattedDimensions($dimensions),
            'limit' => $limit,
            'orderBy' => $orderBy,
        ]);

        $result = collect();

        foreach ($response->getRows() as $row) {
            $rowResult = [];

            foreach ($row->getDimensionValues() as $i => $dimensionValue) {
                $rowResult[$dimensions[$i]] =
                    $this->cast($dimensions[$i], $dimensionValue->getValue());
            }

            foreach ($row->getMetricValues() as $i => $metricValue) {
                $rowResult[$metrics[$i]] =
                    $this->cast($metrics[$i], $metricValue->getValue());
            }

            $result->push($rowResult);
        }

        return $result;
    }

    public function runReport(array $request): RunReportResponse
    {
        $cacheName = $this->determineCacheName(func_get_args());

        if ($this->cacheLifeTimeInMinutes === 0) {
            $this->cache->forget($cacheName);
        }

        return $this->cache->remember($cacheName, $this->cacheLifeTimeInMinutes, function () use ($request) {
            return $this->service->runReport($request);
        });
    }

    public function getAnalyticsService(): BetaAnalyticsDataClient
    {
        return $this->service;
    }

    protected function determineCacheName(array $properties): string
    {
        $hash = md5(serialize($properties));

        return "spatie.laravel-analytics.{$hash}";
    }

    protected function getFormattedMetrics(array $metrics): array
    {
        return collect($metrics)->map(function ($metric) {
            return new Metric([
                'name' => $metric,
            ]);
        })->toArray();
    }

    protected function getFormattedDimensions(array $dimensions): array
    {
        return collect($dimensions)->map(function ($dimension) {
            return new Dimension([
                'name' => $dimension,
            ]);
        })->toArray();
    }

    protected function cast(string $key, string $value): mixed
    {
        return match ($key) {
            'date' => Carbon::createFromFormat('Ymd', $value),
            'visitors', 'pageViews', 'activeUsers', 'newUsers', 'screenPageViews' => (int) $value,
            default => $value,
        };
    }
}
