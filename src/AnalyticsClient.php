<?php

namespace Botble\Analytics;

use DateTimeInterface;
use Google\Service\Analytics\GaData;
use Google_Service_Analytics;
use Illuminate\Contracts\Cache\Repository;

class AnalyticsClient
{
    public function __construct(
        protected Google_Service_Analytics $service,
        protected Repository $cache,
        protected int $cacheLifeTimeInMinutes = 0
    ) {
    }

    public function setCacheLifeTimeInMinutes(int $cacheLifeTimeInMinutes): self
    {
        $this->cacheLifeTimeInMinutes = $cacheLifeTimeInMinutes * 60;

        return $this;
    }

    /**
     * Query the Google Analytics Service with given parameters.
     */
    public function performQuery(
        string $propertyId,
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        string $metrics,
        array $others = []
    ): array|GaData|null {
        $cacheName = $this->determineCacheName(func_get_args());

        if ($this->cacheLifeTimeInMinutes == 0) {
            $this->cache->forget($cacheName);
        }

        return $this->cache->remember(
            $cacheName,
            $this->cacheLifeTimeInMinutes,
            function () use ($propertyId, $startDate, $endDate, $metrics, $others) {
                $result = $this->service->data_ga->get(
                    'ga:' . $propertyId,
                    $startDate->format('Y-m-d'),
                    $endDate->format('Y-m-d'),
                    $metrics,
                    $others
                );

                while ($nextLink = $result->getNextLink()) {
                    if (isset($others['max-results']) && count($result->rows) >= $others['max-results']) {
                        break;
                    }

                    $options = [];

                    parse_str(substr($nextLink, strpos($nextLink, '?') + 1), $options);

                    $response = $this->service->data_ga->call('get', [$options], 'Google_Service_Analytics_GaData');

                    if ($response->rows) {
                        $result->rows = array_merge($result->rows, $response->rows);
                    }

                    $result->nextLink = $response->nextLink;
                }

                return $result;
            }
        );
    }

    protected function determineCacheName(array $properties): string
    {
        return 'analytics.' . md5(serialize($properties));
    }

    /**
     * Determine the cache name for the set of query properties given.
     */
    public function getAnalyticsService(): Google_Service_Analytics
    {
        return $this->service;
    }
}
