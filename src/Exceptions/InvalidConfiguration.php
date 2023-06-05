<?php

namespace Botble\Analytics\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function propertyIdNotSpecified(): self
    {
        return new self(trans('plugins/analytics::analytics.property_id_not_specified'));
    }

    public static function credentialsIsNotValid(): self
    {
        return new self(trans('plugins/analytics::analytics.credential_is_not_valid'));
    }

    public static function invalidPropertyId(): self
    {
        return new self(trans('plugins/analytics::analytics.property_id_is_invalid'));
    }
}
