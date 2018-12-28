<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Container\Builder;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;

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
