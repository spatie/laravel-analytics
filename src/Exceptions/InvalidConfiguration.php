<?php

namespace Spatie\Analytics\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function viewIdNotSpecified(): static
    {
        return new static('There was no view ID specified. You must provide a valid view ID to execute queries on Google Analytics.');
    }

    public static function credentialsJsonDoesNotExist(string $path): static
    {
        return new static("Could not find a credentials file at `{$path}`.");
    }
}
