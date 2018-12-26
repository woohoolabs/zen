<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Builder;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Compiler;
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
        $definitions = $this->getDefinitions();

        $compiler = new Compiler();
        $compiledContainerFiles = $compiler->compile($this->compilerConfig, $definitions);

        if (empty($compiledContainerFiles["definitions"]) === false) {
            throw new ContainerException("RuntimeContainerBuilder doesn't support file-based definitions!");
        }

        $container = substr($compiledContainerFiles["container"], 5);
        eval($container);
    }
}
