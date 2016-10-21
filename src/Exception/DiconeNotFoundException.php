<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Exception;

use Exception;
use Interop\Container\Exception\NotFoundException;

class DiconeNotFoundException extends Exception implements NotFoundException
{
    public function __construct(string $entry)
    {
        parent::__construct("Entry with name '$entry' was not found by Dicone in the compiled container!");
    }
}
