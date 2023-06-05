<?php

namespace Botble\Analytics\GA4\Traits;

use Botble\Analytics\Period;
use Google\Analytics\Data\V1beta\DateRange;

trait DateRangeTrait
{
    public array $dateRanges = [];

    public function dateRange(Period $period): self
    {
        $this->dateRanges[] = (new DateRange())
            ->setStartDate($period->startDate->toDateString())
            ->setEndDate($period->endDate->toDateString());

        return $this;
    }

    public function dateRanges(Period ...$items): self
    {
        foreach ($items as $item) {
            $this->dateRange($item);
        }

        return $this;
    }
}
