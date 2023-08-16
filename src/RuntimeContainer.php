<?php

declare(strict_types=1);

namespace WoohooLabs\Zen;

use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\ContainerDependencyResolver;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\DefinitionInstantiation;
use WoohooLabs\Zen\Exception\NotFoundException;

use function array_key_exists;
use function array_merge;

class RuntimeContainer implements ContainerInterface
{
    private ContainerDependencyResolver $dependencyResolver;
    private DefinitionInstantiation $instantiation;

    public function __construct(AbstractCompilerConfig $compilerConfig)
    {
        $this->dependencyResolver = new ContainerDependencyResolver($compilerConfig);
        $this->instantiation = new DefinitionInstantiation($this);
    }

    public function has(string $id): bool
    {
        if (array_key_exists($id, $this->instantiation->definitions)) {
            return $this->instantiation->definitions[$id]->isEntryPoint();
        }

        try {
            $this->resolve($id);
        } catch (NotFoundException $exception) {
            return false;
        }

        return true;
    }

    /**
     * @throws NotFoundException
     */
    public function get(string $id): mixed
    {
        return $this->instantiation->singletonEntries[$id] ?? ($this->instantiation->definitions[$id] ?? $this->resolve($id))->instantiate($this->instantiation, "");
    }

    /**
     * @throws NotFoundException
     */
    private function resolve(string $id): DefinitionInterface
    {
        $this->instantiation->definitions = array_merge(
            $this->instantiation->definitions,
            $this->dependencyResolver->resolveEntryPoint($id)
        );

        return $this->instantiation->definitions[$id];
    }
}
