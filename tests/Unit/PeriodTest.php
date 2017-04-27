<?php

namespace Spatie\Analytics\Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Spatie\Analytics\Period;
use Spatie\Analytics\Exceptions\InvalidPeriod;

class PeriodTest extends TestCase
{
    /** @test */
    public function it_can_create_a_period_for_a_given_amount_of_days()
    {
        Carbon::setTestNow(Carbon::create(2016, 1, 1));

        $period = Period::days(10);

        $this->assertSame('2015-12-22', $period->startDate->format('Y-m-d'));
        $this->assertSame('2016-01-01', $period->endDate->format('Y-m-d'));
    }

    /** @test */
    public function it_provides_a_create_method()
    {
        $startDate = Carbon::create(2015, 12, 22);
        $endDate = Carbon::create(2016, 1, 1);

        $period = Period::create($startDate, $endDate);

        $this->assertSame('2015-12-22', $period->startDate->format('Y-m-d'));
        $this->assertSame('2016-01-01', $period->endDate->format('Y-m-d'));
    }

    /** @test */
    public function it_will_throw_an_exception_if_the_start_date_comes_after_the_end_date()
    {
        $startDate = Carbon::create(2016, 1, 1);
        $endDate = Carbon::create(2015, 1, 1);

        $this->expectException(InvalidPeriod::class);

        Period::create($startDate, $endDate);
    }
}
