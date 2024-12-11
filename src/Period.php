<?php

namespace Spatie\Analytics;

use Carbon\Carbon;
use DateTimeInterface;
use Google\Analytics\Data\V1beta\DateRange;
use Illuminate\Support\Traits\Macroable;
use Spatie\Analytics\Exceptions\InvalidPeriod;

class Period
{
    use Macroable;

    public DateTimeInterface $startDate;

    public DateTimeInterface $endDate;

    public static function create(DateTimeInterface $startDate, DateTimeInterface $endDate): self
    {
        return new static($startDate, $endDate);
    }

    public static function days(int $numberOfDays): static
    {
        $endDate = Carbon::today();

        $startDate = Carbon::today()->subDays($numberOfDays)->startOfDay();

        return new static($startDate, $endDate);
    }

    public static function months(int $numberOfMonths): static
    {
        $endDate = Carbon::today();

        $startDate = Carbon::today()->subMonths($numberOfMonths)->startOfDay();

        return new static($startDate, $endDate);
    }

    public static function years(int $numberOfYears): static
    {
        $endDate = Carbon::today();

        $startDate = Carbon::today()->subYears($numberOfYears)->startOfDay();

        return new static($startDate, $endDate);
    }

    public function __construct(DateTimeInterface $startDate, DateTimeInterface $endDate)
    {
        if ($startDate > $endDate) {
            throw InvalidPeriod::startDateCannotBeAfterEndDate($startDate, $endDate);
        }

        $this->startDate = $startDate;

        $this->endDate = $endDate;
    }

    public function toDateRange(): DateRange
    {
        return (new DateRange)
            ->setStartDate($this->startDate->format('Y-m-d'))
            ->setEndDate($this->endDate->format('Y-m-d'));
    }
}
