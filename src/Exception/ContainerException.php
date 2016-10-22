<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Exception;

use Exception;
use Interop\Container\Exception\ContainerException as InteropContainerException;

class ContainerException extends Exception implements InteropContainerException
{
}
