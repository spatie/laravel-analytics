<?php namespace Spatie\LaravelAnalytics;

use Exception;
use Google_Client;
use Google_Service_Analytics;
use Illuminate\Contracts\Cache\Repository as CacheContract;

class GoogleApiHelper
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
    protected $cacheLifeTimeInMinutes;

    public function __construct(Google_Client $client, CacheContract $cache)
    {
        $this->service = new Google_Service_Analytics($client);
        $this->cache = $cache;
        $this->cacheLifeTimeInMinutes = 0;
    }

    /**
     * Query the Google Analytics Service with given parameters.
     *
     * @param int    $id
     * @param string $startDate
     * @param string $endDate
     * @param string $metrics
     * @param array  $others
     *
     * @return mixed
     */
    public function performQuery($id, $startDate, $endDate, $metrics, array $others = [])
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
     * @param string $url
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
     * Get all siteIds.
     *
     * @return array
     */
    public function getAllSiteIds()
    {
        static $siteIds = null;

        if (! is_null($siteIds)) {
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
     * Set the cache time.
     *
     * @param  int  $cacheLifeTimeInMinutes
     * @return self
     */
    public function setCacheLifeTimeInMinutes($cacheLifeTimeInMinutes)
    {
        $this->cacheLifeTimeInMinutes = $cacheLifeTimeInMinutes;

        return $this;
    }
}
