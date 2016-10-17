<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Exception;

use Exception;
use Interop\Container\Exception\NotFoundException;

class DiconeNotFoundException extends Exception implements NotFoundException
{
    public function __construct(string $item)
    {
        parent::__construct("Item with name '$item' was not found by Dicone in the compiled container!");
    }
}
