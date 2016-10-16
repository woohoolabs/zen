<?php
namespace WoohooLabs\Dicone\Exception;

use Exception;

class PropertyTypeHintException extends Exception
{
    public function __construct($className, $propertyName)
    {
        parent::__construct("Property '$propertyName' for class '$className' could not be guessed!");
    }
}
