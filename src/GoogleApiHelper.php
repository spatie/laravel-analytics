<?php namespace Spatie\LaravelAnalytics;

use Exception;
use Google_Client;
use Google_Service_Analytics;

class GoogleApiHelper
{
    protected $service;

    protected $cache;

    protected $cacheLifeTimeInMinutes;

    public function __construct(Google_Client $client, $cache)
    {
        $this->service = new Google_Service_Analytics($client);
        $this->cache = $cache;
        $this->cacheLifeTimeInMinutes = 0;
    }

    /**
     * Query the Google Analytics Service with given parameters.
     *
     * @param $id
     * @param $startDate
     * @param $endDate
     * @param $metrics
     * @param array $others
     *
     * @return mixed
     */
    public function performQuery($id, $startDate, $endDate, $metrics, $others = [])
    {
        $cacheName = $this->determineCacheName(func_get_args());

        if ($this->useCache() && $this->cache->has($cacheName)) {

            return $this->cache->get($cacheName);
        }

        $googleAnswer = $this->service->data_ga->get($id, $startDate, $endDate, $metrics, $others);

        if ($this->useCache()) {
            $this->cache->put($cacheName, $googleAnswer, $this->cacheLifeTimeInMinutes);
        }

        return $googleAnswer;
    }

    /**
     * Get a site Id by its URL.
     *
     * @param $url
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getSiteIdByUrl($url)
    {
        $siteIds = $this->getAllSiteIds();

        if (isset($siteIds[$url])) {
            return $siteIds[$url];
        }

        throw new Exception("Site ".$url." is not present in your Analytics account.");
    }

    /**
     * Get all siteIds
     *
     * @return array
     */
    public function getAllSiteIds()
    {
        static $siteIds = null;

        if (! is_null($siteIds))
        {
            return $siteIds;
        }

        foreach ($this->service->management_profiles->listManagementProfiles("~all", "~all") as $site) {
            $siteIds[$site['websiteUrl']] = 'ga:'.$site['id'];
        }

        return $siteIds;
    }

    /**
     * Determine the cache name for the set of query properties given.
     *
     * @param array $properties
     *
     * @return string
     */
    private function determineCacheName(array $properties)
    {
        return 'spatie.laravel-analytics.'.md5(serialize($properties));
    }

    /**
     * Determine if request to Google should be cached.
     *
     * @return bool
     */
    private function useCache()
    {
        return $this->cacheLifeTimeInMinutes > 0;
    }

    /**
     * Set the cache time
     *
     * @param  int   $CacheLifeTimeInMinutes
     * @return $this
     */
    public function setCacheLifeTimeInMinutes($CacheLifeTimeInMinutes)
    {
        $this->cacheLifeTimeInMinutes = $CacheLifeTimeInMinutes;

        return $this;
    }
}
