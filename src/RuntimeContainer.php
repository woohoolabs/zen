<?php
declare(strict_types=1);

namespace WoohooLabs\Zen;

use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\DefinitionInstantiation;
use WoohooLabs\Zen\Container\DependencyResolver;
use WoohooLabs\Zen\Examples\CompilerConfig;
use WoohooLabs\Zen\Exception\NotFoundException;
use function array_merge;

class RuntimeContainer implements ContainerInterface
{
    /**
     * @var CompilerConfig
     */
    private $compilerConfig;

    /**
     * @var DependencyResolver
     */
    private $dependencyResolver;

    /**
     * @var string
     */
    private $rootDirectory;

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

    public function __construct(AbstractCompilerConfig $compilerConfig, string $rootDirectory = "")
    {
        $this->compilerConfig = $compilerConfig;
        $this->dependencyResolver = new DependencyResolver($compilerConfig);
        $this->rootDirectory = $rootDirectory;
        $this->instantiation = new DefinitionInstantiation(
            $this,
            $compilerConfig->getAutoloadConfig(),
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

        $this->resolve($id);

        return isset($this->definitions[$id]) && $this->definitions[$id]->isEntryPoint();
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
     * @return mixed
     * @throws NotFoundException
     */
    private function instantiate(string $id)
    {
        if (isset($this->definitions[$id]) === false) {
            $this->resolve($id);
        }

        $definition = $this->definitions[$id] ?? $this->throwNotFoundException($id);

        return $definition->instantiate($this->instantiation, "");
    }

    private function resolve(string $id): void
    {
        $this->definitions = array_merge($this->definitions, $this->dependencyResolver->resolveEntryPoint($id));
    }

    private function throwNotFoundException(string $id): void
    {
        throw new NotFoundException($id);
    }
}
