<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\RuntimeContainer;

class DefinitionInstantiation
{
    /**
     * @var RuntimeContainer
     */
    private $container;

    /**
     * @var DefinitionInterface[]
     */
    private $definitions;

    /**
     * @var array
     */
    private $singletonEntries;

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

    public function getContainer(): RuntimeContainer
    {
        return $this->container;
    }

    public function getDefinition(string $id): DefinitionInterface
    {
        return $this->definitions[$id];
    }

    /**
     * @return mixed|null
     */
    public function getSingletonEntry(string $id)
    {
        return $this->singletonEntries[$id] ?? null;
    }

    /**
     * @return mixed|null
     */
    public function setSingletonEntry(string $id, $object)
    {
        return $this->singletonEntries[$id] = $object;
    }
}
