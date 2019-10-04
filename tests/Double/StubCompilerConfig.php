<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Tests\Double;

use WoohooLabs\Zen\Config\AbstractCompilerConfig;
use WoohooLabs\Zen\Config\AbstractContainerConfig;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfig;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfigInterface;
use WoohooLabs\Zen\Config\Preload\PreloadConfig;
use WoohooLabs\Zen\Config\Preload\PreloadConfigInterface;

use function dirname;

class StubCompilerConfig extends AbstractCompilerConfig
{
    private string $namespace;
    private string $className;
    private bool $useConstructorInjection;
    private bool $usePropertyInjection;
    private bool $useBuiltInAutoloading;
    /** @var string[] */
    private array $alwaysAutoloadedClasses;
    private bool $useFileBasedDefinition;
    private PreloadConfigInterface $preloadConfig;

    /**
     * @param AbstractContainerConfig[] $containerConfigs
     * @param string[] $alwaysAutoloadedClasses
     */
    public function __construct(
        array $containerConfigs = [],
        string $namespace = "",
        string $className = "",
        bool $useConstructorInjection = true,
        bool $usePropertyInjection = true,
        bool $useBuiltInAutoloading = false,
        array $alwaysAutoloadedClasses = [],
        bool $useFileBasedDefinition = false,
        ?PreloadConfigInterface $preloadConfig = null
    ) {
        $this->namespace = $namespace;
        $this->className = $className;
        $this->containerConfigs = $containerConfigs;
        $this->useConstructorInjection = $useConstructorInjection;
        $this->usePropertyInjection = $usePropertyInjection;
        $this->useBuiltInAutoloading = $useBuiltInAutoloading;
        $this->alwaysAutoloadedClasses = $alwaysAutoloadedClasses;
        $this->useFileBasedDefinition = $useFileBasedDefinition;
        $this->preloadConfig = $preloadConfig ?? new PreloadConfig();
        parent::__construct();
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

    public function getPreloadConfig(): PreloadConfigInterface
    {
        return $this->preloadConfig;
    }

    public function getFileBasedDefinitionConfig(): FileBasedDefinitionConfigInterface
    {
        return FileBasedDefinitionConfig::create($this->useFileBasedDefinition, "Definitions/");
    }

    /**
     * @return AbstractContainerConfig[]
     */
    public function getContainerConfigs(): array
    {
        return $this->containerConfigs;
    }
}
