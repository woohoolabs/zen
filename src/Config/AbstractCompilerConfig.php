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
use WoohooLabs\Zen\Config\Preload\PreloadConfig;
use WoohooLabs\Zen\Config\Preload\PreloadConfigInterface;
use WoohooLabs\Zen\Config\Preload\PreloadInterface;
use function array_key_exists;
use function array_merge;
use function str_replace;

abstract class AbstractCompilerConfig
{
    /**
     * @var ContainerConfigInterface[]
     */
    protected array $containerConfigs = [];

    /**
     * @var EntryPointInterface[]
     */
    protected array $entryPoints;

    /**
     * @var DefinitionHintInterface[]
     */
    protected array $definitionHints;

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

    public function getPreloadConfig(): PreloadConfigInterface
    {
        return PreloadConfig::create();
    }

    public function getFileBasedDefinitionConfig(): FileBasedDefinitionConfigInterface
    {
        return FileBasedDefinitionConfig::disabledGlobally();
    }

    /**
     * @internal
     *
     * @return AbstractContainerConfig[]
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
        $namespace = $this->getContainerNamespace() !== "" ? $this->getContainerNamespace() . "\\" : "";

        return $namespace . $this->getContainerClassName();
    }

    /**
     * @internal
     *
     * @return EntryPointInterface[]
     */
    public function getEntryPointMap(): array
    {
        return $this->entryPoints;
    }

    /**
     * @internal
     *
     * @return PreloadInterface[]
     */
    public function getPreloadMap(): array
    {
        $preloads = [];

        foreach ($this->getPreloadConfig()->getPreloadedClasses() as $preload) {
            foreach ($preload->getClassNames() as $id) {
                $preloads[$id] = $preload;
            }
        }

        return $preloads;
    }

    /**
     * @internal
     *
     * @return DefinitionHintInterface[]
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
                    if (array_key_exists($id, $this->entryPoints) === false) {
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
