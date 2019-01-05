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
use function array_merge;
use function str_replace;

abstract class AbstractCompilerConfig
{
    /**
     * @var ContainerConfigInterface[]
     */
    protected $containerConfigs = [];

    /**
     * @var EntryPointInterface[]
     */
    protected $entryPoints;

    /**
     * @var DefinitionHintInterface[]
     */
    protected $definitionHints;

    public function __construct()
    {
        $this->containerConfigs = $this->getContainerConfigs();
        $this->setEntryPointMap();
        $this->setDefinitionHints();
    }

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
        return $this->entryPoints;
    }

    /**
     * @return DefinitionHintInterface[]
     * @internal
     */
    public function getDefinitionHints(): array
    {
        return $this->definitionHints;
    }

    /**
     * @internal
     */
    protected function setEntryPointMap(): void
    {
        $this->entryPoints = [
            ContainerInterface::class => new ClassEntryPoint(ContainerInterface::class),
            $this->getContainerFqcn() => new ClassEntryPoint($this->getContainerFqcn()),
        ];

        foreach ($this->containerConfigs as $containerConfig) {
            foreach ($containerConfig->createEntryPoints() as $entryPoint) {
                foreach ($entryPoint->getClassNames() as $id) {
                    // TODO This condition is only for ensuring backwards compatibility. It should be removed in Zen 3.0.
                    if (isset($this->entryPoints[$id]) === false) {
                        $this->entryPoints[$id] = $entryPoint;
                    }
                }
            }
        }
    }

    protected function setDefinitionHints(): void
    {
        $definitionHints = [];
        foreach ($this->containerConfigs as $containerConfig) {
            $definitionHints[] = $containerConfig->createDefinitionHints();
        }

        $this->definitionHints = array_merge([], ...$definitionHints);
    }
}
