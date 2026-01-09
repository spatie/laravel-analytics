<?php

namespace Spatie\Analytics\Contracts;

use Spatie\Analytics\AnalyticsClient;

interface Analytics
{
    public function __construct(
        AnalyticsClient $client,
        string $propertyId,
    );
}