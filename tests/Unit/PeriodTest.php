<?php

namespace \Spatie\Analytics\Tests;

use Carbon\Carbon;
use Spatie\Analytics\Period;

class PeriodTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_create_a_period_for_a_given_amount_of_days()
    {
        Carbon::setTestNow(Carbon::create(2016, 1, 1));

        $period = Period::createForNumberOfDays(10);

        $this->assertSame('2016-01-01', $period->endDate->format('Y-m-d'));
        $this->assertSame('2015-12-22', $period->startDate->format('Y-m-d'));
    }
}
