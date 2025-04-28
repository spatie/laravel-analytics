<?php

namespace Spatie\Analytics\Fakes;

/**
 * @mixin \Spatie\Analytics\Analytics
 */
class Analytics
{
    public function __construct(private mixed $result)
    {
        //
    }

    public function __call($method, $args)
    {
        return $this->result;
    }
}
