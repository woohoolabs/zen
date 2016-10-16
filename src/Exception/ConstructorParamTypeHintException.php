<?php
namespace WoohooLabs\Dicone\Exception;

use Exception;

class ConstructorParamTypeHintException extends Exception
{
    public function __construct($className, $paramName)
    {
        parent::__construct("Constructor parameter '$paramName' for class '$className' could not be guessed!");
    }
}
