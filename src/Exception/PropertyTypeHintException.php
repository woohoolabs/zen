<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Exception;

use Exception;

class PropertyTypeHintException extends Exception
{
    public function __construct(string $className, string $propertyName)
    {
        parent::__construct("Type hint for '$className::$" . "$propertyName' could not be guessed!");
    }
}
