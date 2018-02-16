<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Definition;

use WoohooLabs\Zen\Exception\DefinitionNotFoundException;

final class Definitions
{
    /**
     * @var DefinitionInterface[]
     */
    private $definitions;

    /**
     * @param DefinitionInterface[] $definitions
     */
    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    public function setDefinition(string $id, DefinitionInterface $definition): void
    {
        $this->definitions[$id] = $definition;
    }

    public function hasDefinition(string $id): bool
    {
        return isset($this->definitions[$id]);
    }

    /**
     * @throws DefinitionNotFoundException
     */
    public function getDefinition(string $id): DefinitionInterface
    {
        if (isset($this->definitions[$id]) === false) {
            throw new DefinitionNotFoundException($id);
        }

        return $this->definitions[$id];
    }

    /**
     * @return DefinitionInterface[]
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }
}
