<?php
namespace WoohooLabs\Dicone\Exception;

use Exception;

class PropertyTypeHintException extends Exception
{
    public function __construct($className, $propertyName)
    {
        parent::__construct("Type hint for '$className::$" . "$propertyName' could not be guessed!");
    }
}
