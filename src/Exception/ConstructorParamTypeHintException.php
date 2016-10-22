<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Exception;

use Exception;

class ConstructorParamTypeHintException extends Exception
{
    public function __construct(string $className, string $paramName)
    {
        parent::__construct("Constructor parameter '$paramName' for class '$className' could not be guessed!");
    }
}
