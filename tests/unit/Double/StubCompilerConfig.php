<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Unit\Double;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;

class StubCompilerConfig extends AbstractCompilerConfig
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $className;

    /**
     * @var array
     */
    private $containerConfigs;

    /**
     * @var bool
     */
    private $useConstructorInjection;

    /**
     * @var bool
     */
    private $usePropertyInjection;

    public function __construct(
        array $containerConfigs = [],
        string $namespace = "",
        string $className = "",
        bool $useConstructorInjection = true,
        bool $usePropertyInjection = true
    ) {
        $this->namespace = $namespace;
        $this->className = $className;
        $this->containerConfigs = $containerConfigs;
        $this->useConstructorInjection = $useConstructorInjection;
        $this->usePropertyInjection = $usePropertyInjection;
    }

    public function getContainerNamespace(): string
    {
        return $this->namespace;
    }

    public function getContainerClassName(): string
    {
        return $this->className;
    }

    public function useConstructorInjection(): bool
    {
        return $this->useConstructorInjection;
    }

    public function usePropertyInjection(): bool
    {
        return $this->usePropertyInjection;
    }

    public function getContainerConfigs(): array
    {
        return $this->containerConfigs;
    }
}
