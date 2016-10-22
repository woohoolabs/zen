<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Exception;

use Exception;
use Interop\Container\Exception\ContainerException;

class ZenContainerException extends Exception implements ContainerException
{
}
