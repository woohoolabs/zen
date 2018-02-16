<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Exception;

use Exception;

class DefinitionNotFoundException extends Exception
{
    public function __construct(string $id)
    {
        parent::__construct("Definition with '$id' ID can not be found!");
    }
}
