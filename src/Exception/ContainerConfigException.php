<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Exception;

use Exception;

class ContainerConfigException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
