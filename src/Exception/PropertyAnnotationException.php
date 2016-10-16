<?php
namespace WoohooLabs\Dicone\Exception;

use Exception;

class PropertyAnnotationException extends Exception
{
    public function __construct($className, $propertyName)
    {
        parent::__construct("Property '$propertyName' of class '$className' could not be guessed!");
    }
}
