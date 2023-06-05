<?php

namespace Botble\Analytics;

use Botble\Analytics\Exceptions\InvalidPeriod;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class Period
{
    public function __construct(public CarbonInterface $startDate, public CarbonInterface $endDate)
    {
        if ($startDate > $endDate) {
            throw InvalidPeriod::startDateCannotBeAfterEndDate($startDate, $endDate);
        }
    }

    public static function create(CarbonInterface $startDate, CarbonInterface $endDate): self
    {
        return new self($startDate, $endDate);
    }

    public static function days(int $numberOfDays): self
    {
        $endDate = Carbon::today();

        $startDate = Carbon::today()->subDays($numberOfDays)->startOfDay();

        return new self($startDate, $endDate);
    }

    public static function months(int $numberOfMonths): self
    {
        $endDate = Carbon::today();

        $startDate = Carbon::today()->subMonths($numberOfMonths)->startOfDay();

        return new self($startDate, $endDate);
    }

    public static function years(int $numberOfYears): self
    {
        $endDate = Carbon::today();

        $startDate = Carbon::today()->subYears($numberOfYears)->startOfDay();

        return new self($startDate, $endDate);
    }
}
