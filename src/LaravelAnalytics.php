<?php namespace Spatie\LaravelAnalytics;

use Illuminate\Support\Collection;
use Cache;
use Spatie\LaravelAnalytics\Helpers\GoogleApiHelper;
use Carbon\Carbon;

class LaravelAnalytics
{
    /**
     * @var Analytics
     */
    protected $client;
    /**
     * @var string
     */
    protected $siteId;
    /**
     * @var int
     */
    protected $cacheLifeTimeInMinutes;

    /**
     * @param GoogleApiHelper $client                 An already authenticated client
     * @param string          $siteId                 Should look something like ga:xxxxxxxxx
     * @param int             $cacheLifeTimeInMinutes
     */
    public function __construct(GoogleApiHelper $client, $siteId = '', $cacheLifeTimeInMinutes = 0)
    {
        $this->client = $client;
        $this->siteId = $siteId;
        $this->cacheLifeTimeInMinutes = $cacheLifeTimeInMinutes;
    }

    /**
     * Get the amount of visitors and pageviews.
     *
     * @param int    $numberOfDays
     * @param string $groupBy      Possible values: date, yearMonth
     *
     * @return Collection
     */
    public function getVisitorsAndPageViews($numberOfDays = 365, $groupBy = 'date')
    {
        list($startDate, $endDate) = $this->calculateNumberOfDays($numberOfDays);

        return $this->getVisitorsAndPageViewsForPeriod($startDate, $endDate, $groupBy);
    }

    /**
     * Get the amount of visitors and pageviews for the given period.
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string    $groupBy   Possible values: date, yearMonth
     *
     * @return Collection
     */
    public function getVisitorsAndPageViewsForPeriod($startDate, $endDate, $groupBy = 'date')
    {
        $visitorData = [];
        $answer = $this->performQuery($startDate, $endDate, 'ga:visits,ga:pageviews', ['dimensions' => 'ga:'.$groupBy]);

        foreach ($answer->rows as $dateRow) {
            $visitorData[] = [$groupBy => Carbon::createFromFormat(($groupBy == 'yearMonth' ? 'Ym' : 'Ymd'), $dateRow[0]), 'visitors' => $dateRow[1], 'pageViews' => $dateRow[2]];
        }

        return new Collection($visitorData);
    }

    /**
     * Get the top keywords.
     *
     * @param int $numberOfDays
     * @param int $maxResults
     *
     * @return Collection
     */
    public function getTopKeywords($numberOfDays = 365, $maxResults = 30)
    {
        list($startDate, $endDate) = $this->calculateNumberOfDays($numberOfDays);

        return $this->getTopKeyWordsForPeriod($startDate, $endDate, $maxResults);
    }

    /**
     * Get the top keywords for the given period.
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int       $maxResults
     *
     * @return Collection
     */
    public function getTopKeyWordsForPeriod($startDate, $endDate, $maxResults = 30)
    {
        $keywordData = [];

        $answer = $this->performQuery($startDate, $endDate, 'ga:sessions', ['dimensions' => 'ga:keyword', 'sort' => '-ga:sessions', 'max-results' => $maxResults, 'filters' => 'ga:keyword!=(not set);ga:keyword!=(not provided)']);

        if (is_null($answer->rows)) {
            return new Collection([]);
        }

        foreach ($answer->rows as $pageRow) {
            $keywordData[] = ['keyword' => $pageRow[0], 'sessions' => $pageRow[1]];
        }

        return new Collection($keywordData);
    }

    /**
     * Get the top referrers.
     *
     * @param int $numberOfDays
     * @param int $maxResults
     *
     * @return Collection
     */
    public function getTopReferrers($numberOfDays = 365, $maxResults = 20)
    {
        list($startDate, $endDate) = $this->calculateNumberOfDays($numberOfDays);

        return $this->getTopReferrersForPeriod($startDate, $endDate, $maxResults);
    }

    /**
     * Get the top referrers for the given period.
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param $maxResults
     *
     * @return Collection
     */
    public function getTopReferrersForPeriod($startDate, $endDate, $maxResults)
    {
        $referrerData = [];

        $answer = $this->performQuery($startDate, $endDate, 'ga:pageviews', ['dimensions' => 'ga:fullReferrer', 'sort' => '-ga:pageviews', 'max-results' => $maxResults]);

        if (is_null($answer->rows)) {
            return new Collection([]);
        }

        foreach ($answer->rows as $pageRow) {
            $referrerData[] = ['url' => $pageRow[0], 'pageViews' => $pageRow[1]];
        }

        return new Collection($referrerData);
    }

    /**
     * Get the top browsers.
     *
     * @param int $numberOfDays
     * @param int $maxResults
     *
     * @return Collection
     */
    public function getTopBrowsers($numberOfDays = 365, $maxResults = 6)
    {
        list($startDate, $endDate) = $this->calculateNumberOfDays($numberOfDays);

        return $this->getTopBrowsersForPeriod($startDate, $endDate, $maxResults);
    }

    /**
     * Get the top browsers for the given period.
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param $maxResults
     *
     * @return Collection
     */
    public function getTopBrowsersForPeriod($startDate, $endDate, $maxResults)
    {
        $browserData = [];
        $answer = $this->performQuery($startDate, $endDate, 'ga:sessions', ['dimensions' => 'ga:browser', 'sort' => '-ga:sessions']);

        if (is_null($answer->rows)) {
            return new Collection([]);
        }

        foreach ($answer->rows as $browserRow) {
            $browserData[] = ['browser' => $browserRow[0], 'sessions' => $browserRow[1]];
        }

        $browserCollection = new Collection(array_slice($browserData, 0, $maxResults - 1));

        if (count($browserData) > $maxResults) {
            $otherBrowsers = new Collection(array_slice($browserData, $maxResults - 1));
            $otherBrowsersCount = array_sum($otherBrowsers->lists('sessions'));

            $browserCollection->put(null, ['browser' => 'other', 'sessions' => $otherBrowsersCount]);
        }

        return $browserCollection;
    }

    /**
     * Get the most visited pages.
     *
     * @param int $numberOfDays
     * @param int $maxResults
     *
     * @return Collection
     */
    public function getMostVisitedPages($numberOfDays = 365, $maxResults = 20)
    {
        list($startDate, $endDate) = $this->calculateNumberOfDays($numberOfDays);

        return $this->getMostVisitedPagesForPeriod($startDate, $endDate, $maxResults);
    }

    /**
     * Get the most visited pages for the given period.
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int       $maxResults
     *
     * @return Collection
     */
    public function getMostVisitedPagesForPeriod($startDate, $endDate, $maxResults = 20)
    {
        $pagesData = [];

        $answer = $this->performQuery($startDate, $endDate, 'ga:pageviews', ['dimensions' => 'ga:pagePath', 'sort' => '-ga:pageviews', 'max-results' => $maxResults]);

        if (is_null($answer->rows)) {
            return new Collection([]);
        }

        foreach ($answer->rows as $pageRow) {
            $pagesData[] = ['url' => $pageRow[0], 'pageViews' => $pageRow[1]];
        }

        return new Collection($pagesData);
    }

    /**
     * Returns the site id (ga:xxxxxxx) for the given url.
     *
     * @param $url
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getSiteIdByUrl($url)
    {
        return $this->client->getSiteIdByUrl($url);
    }

    /**
     * Call the query method on the autenthicated client.
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param $metrics
     * @param array     $others
     *
     * @return mixed
     */
    public function performQuery($startDate, $endDate, $metrics, $others = array())
    {
        $cacheName = $this->determineCacheName(func_get_args());

        if ($this->useCache() and Cache::has($cacheName)) {
            return Cache::get($cacheName);
        }

        $answer = $this->client->query($this->siteId, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'), $metrics, $others);

        if ($this->useCache()) {
            Cache::put($cacheName, $answer, $this->cacheLifeTimeInMinutes);
        }

        return $answer;
    }

    /**
     * Return true if this site is configured to use Google Analytics.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return !$this->siteId == '';
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
        return 'spatie.laravelanalytics.'.md5(serialize($properties));
    }

    /**
     * Returns an array with the current date and the date minus the number of days specified.
     *
     * @param $numberOfDays
     *
     * @return array
     */
    private function calculateNumberOfDays($numberOfDays)
    {
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($numberOfDays);

        return [$startDate, $endDate];
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
}
