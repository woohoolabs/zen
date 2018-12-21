<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
use function dirname;

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

    /**
     * @var bool
     */
    private $useBuiltInAutoloading;

    /**
     * @var string[]
     */
    private $alwaysAutoloadedClasses;

    public function __construct(
        array $containerConfigs = [],
        string $namespace = "",
        string $className = "",
        bool $useConstructorInjection = true,
        bool $usePropertyInjection = true,
        bool $useBuiltInAutoloading = false,
        array $alwaysAutoloadedClasses = []
    ) {
        $this->namespace = $namespace;
        $this->className = $className;
        $this->containerConfigs = $containerConfigs;
        $this->useConstructorInjection = $useConstructorInjection;
        $this->usePropertyInjection = $usePropertyInjection;
        $this->useBuiltInAutoloading = $useBuiltInAutoloading;
        $this->alwaysAutoloadedClasses = $alwaysAutoloadedClasses;
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

    public function getAutoloadConfig(): AutoloadConfigInterface
    {
        return AutoloadConfig::create($this->useBuiltInAutoloading, dirname(__DIR__, 2))
            ->setAlwaysAutoloadedClasses($this->alwaysAutoloadedClasses);
    }

    public function getContainerConfigs(): array
    {
        return $this->containerConfigs;
    }
}
