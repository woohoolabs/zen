<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Builder;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;
use WoohooLabs\Zen\Container\DependencyResolver;

abstract class AbstractContainerBuilder implements ContainerBuilderInterface
{
    /**
     * @var AbstractCompilerConfig
     */
    protected $compilerConfig;

    public function __construct(AbstractCompilerConfig $compilerConfig)
    {
        $this->compilerConfig = $compilerConfig;
    }
}
