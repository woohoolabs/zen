<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface as PsrNotFoundExceptionInterface;

class NotFoundException extends Exception implements PsrNotFoundExceptionInterface
{
    public function __construct(string $id)
    {
        parent::__construct("Entry with ID '$id' was not found in the container!");
    }
}
