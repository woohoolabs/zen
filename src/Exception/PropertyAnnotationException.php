<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Exception;

use Exception;

class PropertyAnnotationException extends Exception
{
    public function __construct(string $className, string $propertyName)
    {
        parent::__construct("Property '$propertyName' of class '$className' could not be guessed!");
    }
}
