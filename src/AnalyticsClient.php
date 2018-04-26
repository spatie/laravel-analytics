<?php

namespace Spatie\Analytics;

use DateTime;
use Google_Service_Analytics;
use Illuminate\Contracts\Cache\Repository;
use Psr\Log\LoggerInterface;

class AnalyticsClient
{
    /** @var \Google_Service_Analytics */
    protected $service;

    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $cache;

    /** @var LoggerInterface */
    protected $log;

    /** @var int */
    protected $cacheLifeTimeInMinutes = 0;

    public function __construct(Google_Service_Analytics $service, Repository $cache, LoggerInterface $log)
    {
        $this->service = $service;

        $this->cache = $cache;

        $this->log = $log;
    }

    /**
     * Set the cache time.
     *
     * @param int $cacheLifeTimeInMinutes
     *
     * @return self
     */
    public function setCacheLifeTimeInMinutes(int $cacheLifeTimeInMinutes)
    {
        $this->cacheLifeTimeInMinutes = $cacheLifeTimeInMinutes;

        return $this;
    }

    /**
     * Query the Google Analytics Service with given parameters.
     *
     * @param string    $viewId
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string    $metrics
     * @param array     $others
     *
     * @return array|null
     */
    public function performQuery(string $viewId, DateTime $startDate, DateTime $endDate, string $metrics, array $others = [])
    {
        $cacheName = $this->determineCacheName(func_get_args());

        if ($this->cacheLifeTimeInMinutes == 0) {
            $this->cache->forget($cacheName);
        }

        return $this->cache->remember($cacheName, $this->cacheLifeTimeInMinutes, function () use ($viewId, $startDate, $endDate, $metrics, $others) {
            $this->log->notice('[AnalyticsClient.performQuery] Fetching - first page');
            $result = $this->service->data_ga->get(
                "ga:{$viewId}",
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
                $metrics,
                $others
            );

            $nextLink = $result->getNextLink();

            while ($nextLink) {
                $options = [];
                /**
                 * @source https://stackoverflow.com/a/33526740
                 */
                parse_str(substr($nextLink, strpos($nextLink, '?') + 1), $options);

                $percentage = $options['start-index'] * 100 / $result->getTotalResults();
                $this->log->notice(sprintf('[AnalyticsClient.performQuery] Fetching - paging active %.2f%% of %d [%s]',
                    $percentage, $result->getTotalResults(), $nextLink)
                );

                $data = $this->service->data_ga->call('get', [$options], "Google_Service_Analytics_GaData");

                if ($data->rows) {
                    $result->rows = array_merge($result->rows, $data->rows);
                }

                $nextLink = $data->getNextLink();

            }

            return $result;
        });
    }

    public function getAnalyticsService(): Google_Service_Analytics
    {
        return $this->service;
    }

    /*
     * Determine the cache name for the set of query properties given.
     */
    protected function determineCacheName(array $properties): string
    {
        return 'spatie.laravel-analytics.'.md5(serialize($properties));
    }
}
