<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\RuntimeContainer;

final class DefinitionInstantiation
{
    /**
     * @var RuntimeContainer
     */
    public $container;

    /**
     * @var DefinitionInterface[]
     */
    public $definitions;

    /**
     * @var array
     */
    public $singletonEntries;

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function __construct(
        RuntimeContainer $container,
        array &$definitions,
        array &$singletonEntries
    ) {
        $this->container = $container;
        $this->definitions = &$definitions;
        $this->singletonEntries = &$singletonEntries;
    }
}
