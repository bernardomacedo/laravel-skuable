<?php

namespace Bernardomacedo\Skuable;

use Exception;

class InvalidOption extends Exception
{
    public static function missingFromField()
    {
        return new static('Could not determine which fields should be Skuified');
    }

    public static function missingSkuField()
    {
        return new static('Could not determine in which field the Sku should be saved');
    }
}
