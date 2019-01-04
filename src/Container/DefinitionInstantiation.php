<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container;

use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\RuntimeContainer;

class DefinitionInstantiation
{
    /**
     * @var RuntimeContainer
     */
    private $container;

    /**
     * @var AutoloadConfigInterface
     */
    private $autoloadConfig;

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
        AutoloadConfigInterface $autoloadConfig,
        array &$definitions,
        array &$singletonEntries
    ) {
        $this->container = $container;
        $this->autoloadConfig = $autoloadConfig;
        $this->definitions = &$definitions;
        $this->singletonEntries = &$singletonEntries;
    }

    public function getContainer(): RuntimeContainer
    {
        return $this->container;
    }

    public function getAutoloadConfig(): AutoloadConfigInterface
    {
        return $this->autoloadConfig;
    }

    /**
     * @return DefinitionInterface[]
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
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
