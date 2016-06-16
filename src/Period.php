<?php

namespace Spatie\LaravelAnalytics;

use Carbon\Carbon;
use DateTime;

class Period
{
    /** @var \DateTime */
    protected $startDate;

    /** @var \DateTime */
    protected $endDate;

    public static function createForNumberOfDays(int $numberOfDays)
    {
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($numberOfDays);

        return new static($startDate, $endDate);
    }

    public function __construct(DateTime $startDate, DateTime $endDate)
    {
        $this->startDate = $startDate;

        $this->endDate = $endDate;
    }



}