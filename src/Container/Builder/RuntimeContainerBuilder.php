<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Builder;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Compiler;
use WoohooLabs\Zen\Container\DependencyResolver;
use WoohooLabs\Zen\Exception\ContainerException;
use function substr;

class RuntimeContainerBuilder extends AbstractContainerBuilder
{
    public function __construct(AbstractCompilerConfig $compilerConfig)
    {
        parent::__construct($compilerConfig);
    }

    public function build(): void
    {
        $dependencyResolver = new DependencyResolver($this->compilerConfig);

        $compiler = new Compiler();
        $compiledContainerFiles = $compiler->compile($this->compilerConfig, $dependencyResolver->resolveEntryPoints());

        if (empty($compiledContainerFiles["definitions"]) === false) {
            throw new ContainerException("RuntimeContainerBuilder doesn't support file-based definitions!");
        }

        $container = substr($compiledContainerFiles["container"], 5);
        eval($container);
    }
}
