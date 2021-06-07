<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\RuntimeContainer;

final class DefinitionInstantiation
{
    public RuntimeContainer $container;
    /** @var DefinitionInterface[] */
    public array $definitions = [];
    /** @var array<string, mixed> */
    public array $singletonEntries = [];

    public function __construct(RuntimeContainer $container)
    {
        $this->container = $container;
    }
}
