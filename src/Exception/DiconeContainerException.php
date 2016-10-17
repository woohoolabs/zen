<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Exception;

use Exception;
use Interop\Container\Exception\ContainerException;

class DiconeContainerException extends Exception implements ContainerException
{
}
