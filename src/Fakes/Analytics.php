<?php

namespace Spatie\Analytics\Fakes;

use Illuminate\Support\Collection;

/**
 * @mixin \Spatie\Analytics\Analytics
 */
class Analytics
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