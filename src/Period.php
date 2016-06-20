<?php

namespace Spatie\Analytics;

use Carbon\Carbon;
use DateTime;

class Period
{
    /** @var \DateTime */
    public $startDate;

    /** @var \DateTime */
    public $endDate;

    public static function create(DateTime $startDate, $endDate): Period
    {
        return new static($startDate, $endDate);
    }

    public static function days(int $numberOfDays): Period
    {
        $endDate = Carbon::today();

        $startDate = Carbon::today()->subDays($numberOfDays)->startOfDay();

        return new static($startDate, $endDate);
    }

    public function __construct(DateTime $startDate, DateTime $endDate)
    {
        $this->startDate = $startDate;

        $this->endDate = $endDate;
    }
}
