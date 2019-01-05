<?php
declare(strict_types=1);

namespace WoohooLabs\Zen;

use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\DefinitionInstantiation;
use WoohooLabs\Zen\Container\DependencyResolver;
use WoohooLabs\Zen\Exception\NotFoundException;
use function array_merge;

class RuntimeContainer implements ContainerInterface
{
    /**
     * @var DependencyResolver
     */
    private $dependencyResolver;

    /**
     * @var DefinitionInterface[]
     */
    private $definitions = [];

    /**
     * @var array
     */
    private $singletonEntries = [];

    /**
     * @var DefinitionInstantiation
     */
    private $instantiation;

    public function __construct(AbstractCompilerConfig $compilerConfig)
    {
        $this->dependencyResolver = new DependencyResolver($compilerConfig);
        $this->instantiation = new DefinitionInstantiation(
            $this,
            $this->definitions,
            $this->singletonEntries
        );
    }

    /**
     * @param string $id
     */
    public function has($id): bool
    {
        if (isset($this->definitions[$id])) {
            return $this->definitions[$id]->isEntryPoint();
        }

        try {
            $this->resolve($id);
        } catch (NotFoundException $exception) {
            return false;
        }

        return true;
    }

    /**
     * @param string $id
     * @return mixed
     * @throws NotFoundException
     */
    public function get($id)
    {
        return $this->singletonEntries[$id] ?? $this->instantiate($id);
    }

    /**
     * @param string $id
     * @return DefinitionInterface
     * @throws NotFoundException
     */
    private function instantiate(string $id)
    {
        if (isset($this->definitions[$id]) === false) {
            $this->resolve($id);
        }

        return $this->definitions[$id]->instantiate($this->instantiation, "");
    }

    /**
     * @throws NotFoundException
     */
    private function resolve($id)
    {
        $this->definitions = array_merge($this->definitions, $this->dependencyResolver->resolveEntryPoint($id));
    }
}
