<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Exception;

use Exception;
use Interop\Container\Exception\NotFoundException as InteropNotFoundException;

class NotFoundException extends Exception implements InteropNotFoundException
{
    public function __construct(string $id)
    {
        parent::__construct("Entry with ID '$id' was not found in the compiled container!");
    }
}
