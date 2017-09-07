<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Builder;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Compiler;

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
        $compiledContainer = $compiler->compile($this->compilerConfig, $definitions);
        $compiledContainer = substr($compiledContainer, 5);
        eval($compiledContainer);
    }
}
