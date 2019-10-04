<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface as PsrContainerExceptionInterface;

class ContainerException extends Exception implements PsrContainerExceptionInterface
{
}
