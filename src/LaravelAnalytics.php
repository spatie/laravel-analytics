<?php

namespace Spatie\LaravelAnalytics;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;

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
     * @param GoogleApiHelper $client
     * @param string          $siteId
     */
    public function __construct(GoogleApiHelper $client, $siteId = '')
    {
        $this->client = $client;
        $this->siteId = $siteId;
    }

    /**
     * Set the siteId.
     *
     * @param string $siteId
     *
     * @return $this
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;

        return $this;
    }

    /**
     * Get the siteId
     *
     * @return string $siteId
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * Get the amount of visitors and pageViews.
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
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string   $groupBy   Possible values: date, yearMonth
     *
     * @return Collection
     */
    public function getVisitorsAndPageViewsForPeriod(DateTime $startDate, DateTime $endDate, $groupBy = 'date')
    {
        $visitorData = [];
        $answer = $this->performQuery($startDate, $endDate, 'ga:visits,ga:pageviews', ['dimensions' => 'ga:'.$groupBy]);

        if (is_null($answer->rows)) {
            return new Collection([]);
        }

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
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int      $maxResults
     *
     * @return Collection
     */
    public function getTopKeyWordsForPeriod(DateTime $startDate, DateTime $endDate, $maxResults = 30)
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
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int      $maxResults
     *
     * @return Collection
     */
    public function getTopReferrersForPeriod(DateTime $startDate, DateTime $endDate, $maxResults)
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
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int      $maxResults
     *
     * @return Collection
     */
    public function getTopBrowsersForPeriod(DateTime $startDate, DateTime $endDate, $maxResults)
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
            $otherBrowsersCount = array_sum(Collection::make($otherBrowsers->lists('sessions'))->toArray());

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
     * Get the number of active users currently on the site.
     *
     * @param array $others
     *
     * @return int
     */
    public function getActiveUsers($others = array())
    {
        $answer = $this->performRealTimeQuery('rt:activeUsers', $others);

        if (is_null($answer->rows)) {
            return 0;
        }

        return $answer->rows[0][0];
    }

    /**
     * Get the most visited pages for the given period.
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int      $maxResults
     *
     * @return Collection
     */
    public function getMostVisitedPagesForPeriod(DateTime $startDate, DateTime $endDate, $maxResults = 20)
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
     * @param string $url
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
     * Call the query method on the authenticated client.
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string   $metrics
     * @param array    $others
     *
     * @return mixed
     */
    public function performQuery(DateTime $startDate, DateTime $endDate, $metrics, $others = array())
    {
        return $this->client->performQuery($this->siteId, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'), $metrics, $others);
    }

    /**
     * Call the real time query method on the authenticated client.
     *
     * @param string $metrics
     * @param array  $others
     *
     * @return mixed
     */
    public function performRealTimeQuery($metrics, $others = array())
    {
        return $this->client->performRealTimeQuery($this->siteId, $metrics, $others);
    }

    /**
     * Return true if this site is configured to use Google Analytics.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->siteId != '';
    }

    /**
     * Returns an array with the current date and the date minus the number of days specified.
     *
     * @param int $numberOfDays
     *
     * @return array
     */
    protected function calculateNumberOfDays($numberOfDays)
    {
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($numberOfDays);

        return [$startDate, $endDate];
    }
}
