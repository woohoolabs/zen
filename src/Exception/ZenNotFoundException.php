<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Exception;

use Exception;
use Interop\Container\Exception\NotFoundException;

class ZenNotFoundException extends Exception implements NotFoundException
{
    public function __construct(string $id)
    {
        parent::__construct("Entry with ID '$id' was not found by Zen in the compiled container!");
    }
}
