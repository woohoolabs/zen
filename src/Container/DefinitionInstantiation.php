<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\RuntimeContainer;

final class DefinitionInstantiation
{
    /** @var RuntimeContainer */
    public $container;
    /** @var DefinitionInterface[] */
    public $definitions = [];
    /** @var array<string, mixed> */
    public $singletonEntries = [];

    public function __construct(RuntimeContainer $container)
    {
        $this->container = $container;
    }
}
