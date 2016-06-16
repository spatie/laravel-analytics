<?php

namespace Spatie\Analytics;

use Google_Service_Analytics;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Contracts\Cache\Repository;

class Service
{
    /**
     * @var Google_Service_Analytics
     */
    protected $service;

    /**
     * @var CacheContract
     */
    protected $cache;

    /**
     * @var int
     */
    protected $cacheLifeTimeInMinutes = 0;

    public function __construct(Google_Service_Analytics $service, Repository $cache)
    {
        $this->service = $service;
        
        $this->cache = $cache;
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
     * @param int    $siteId
     * @param string $startDate
     * @param string $endDate
     * @param string $metrics
     * @param array  $others
     *
     * @return mixed
     */
    public function performQuery($siteId, $startDate, $endDate, $metrics, array $others = [])
    {
        $cacheName = $this->determineCacheName(func_get_args());
        
        return $this->cache->remember($cacheName, $this->cacheLifeTimeInMinutes, function() use ($siteId, $startDate, $endDate, $metrics, $others)  {
           return $this->service->data_ga->get($siteId, $startDate, $endDate, $metrics, $others);
        });
    }
    
    /**
     * Determine the cache name for the set of query properties given.
     *
     * @param array $properties
     *
     * @return string
     */
    protected function determineCacheName(array $properties)
    {
        return 'spatie.laravel-analytics.'.md5(serialize($properties));
    }
}
