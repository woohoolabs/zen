<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config;

use Psr\Container\ContainerInterface;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfig;
use WoohooLabs\Zen\Config\Autoload\AutoloadConfigInterface;
use WoohooLabs\Zen\Config\EntryPoint\ClassEntryPoint;
use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfig;
use WoohooLabs\Zen\Config\FileBasedDefinition\FileBasedDefinitionConfigInterface;
use WoohooLabs\Zen\Config\Hint\DefinitionHintInterface;
use function str_replace;

abstract class AbstractCompilerConfig
{
    abstract public function getContainerNamespace(): string;

    abstract public function getContainerClassName(): string;

    abstract public function useConstructorInjection(): bool;

    abstract public function usePropertyInjection(): bool;

    public function getAutoloadConfig(): AutoloadConfigInterface
    {
        return AutoloadConfig::disabledGlobally();
    }

    public function getFileBasedDefinitionConfig(): FileBasedDefinitionConfigInterface
    {
        return FileBasedDefinitionConfig::disabledGlobally();
    }

    /**
     * @return AbstractContainerConfig[]
     * @internal
     */
    abstract public function getContainerConfigs(): array;

    /**
     * @internal
     */
    public function getContainerHash(): string
    {
        return str_replace("\\", "__", $this->getContainerFqcn());
    }

    /**
     * @internal
     */
    public function getContainerFqcn(): string
    {
        $namespace = $this->getContainerNamespace() ? $this->getContainerNamespace() . "\\" : "";

        return $namespace . $this->getContainerClassName();
    }

    /**
     * @return EntryPointInterface[]
     * @internal
     */
    public function getEntryPointMap(): array
    {
        $entryPoints = [
            $this->getContainerFqcn() => new ClassEntryPoint($this->getContainerFqcn()),
            ContainerInterface::class => new ClassEntryPoint(ContainerInterface::class),
        ];

        foreach ($this->getContainerConfigs() as $containerConfig) {
            foreach ($containerConfig->createEntryPoints() as $entryPoint) {
                foreach ($entryPoint->getClassNames() as $id) {
                    $entryPoints[$id] = $entryPoint;
                }
            }
        }

        return $entryPoints;
    }

    /**
     * @return DefinitionHintInterface[]
     */
    public function getDefinitionHints(): array
    {
        $definitionHints = [];

        foreach ($this->getContainerConfigs() as $containerConfig) {
            $definitionHints = array_merge($definitionHints, $containerConfig->createDefinitionHints());
        }

        return $definitionHints;
    }
}
