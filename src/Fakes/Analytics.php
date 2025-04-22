<?php

namespace Spatie\Analytics\Fakes;

use Illuminate\Support\Collection;
use Illuminate\Support\Testing\Fakes\Fake;

/**
 * @mixin \Spatie\Analytics\Analytics
 */
class Analytics implements Fake
{
    /**
     * @param array|Collection $result
     */
    public function __construct(private $result)
    {
        //
    }

    public function __call($method, $args)
    {
        return $this->result;
    }
}