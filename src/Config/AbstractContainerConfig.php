<?php

declare(strict_types=1);

namespace WoohooLabs\Zen\Config;

use WoohooLabs\Zen\Config\EntryPoint\ClassEntryPoint;
use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Config\Hint\DefinitionHintInterface;
use WoohooLabs\Zen\Config\Hint\WildcardHintInterface;
use WoohooLabs\Zen\Exception\ContainerException;

use function array_map;
use function array_merge;
use function is_string;

abstract class AbstractContainerConfig implements ContainerConfigInterface
{
    /** @var EntryPointInterface[] */
    protected array $entryPoints;
    /** @var DefinitionHintInterface[] */
    protected array $definitionHints;

    public function __construct()
    {
        $this->setEntryPoints();
        $this->setDefinitionHints();
    }

    /**
     * @return EntryPointInterface[]|string[]
     */
    abstract protected function getEntryPoints(): array;

    /**
     * @return DefinitionHintInterface[]|string[]
     */
    abstract protected function getDefinitionHints(): array;

    /**
     * @return WildcardHintInterface[]
     */
    abstract protected function getWildcardHints(): array;

    /**
     * @internal
     *
     * @return EntryPointInterface[]
     */
    public function createEntryPoints(): array
    {
        return $this->entryPoints;
    }

    /**
     * @internal
     *
     * @return DefinitionHintInterface[]
     */
    public function createDefinitionHints(): array
    {
        return $this->definitionHints;
    }

    /**
     * @internal
     */
    protected function setEntryPoints(): void
    {
        $this->entryPoints = array_map(
            static function ($entryPoint): EntryPointInterface {
                if ($entryPoint instanceof EntryPointInterface) {
                    return $entryPoint;
                }

                if (is_string($entryPoint)) {
                    return new ClassEntryPoint($entryPoint);
                }

                throw new ContainerException(
                    "An entry point must be either a string or instance of the EntryPointInterface (e.g.: ClassEntryPoint)!"
                );
            },
            $this->getEntryPoints()
        );
    }

    /**
     * @internal
     */
    protected function setDefinitionHints(): void
    {
        $this->definitionHints = array_map(
            static function ($definitionHint): DefinitionHintInterface {
                if ($definitionHint instanceof DefinitionHintInterface) {
                    return $definitionHint;
                }

                if (is_string($definitionHint)) {
                    return new DefinitionHint($definitionHint);
                }

                throw new ContainerException("A definition hint must be either a string or a DefinitionHint object!");
            },
            $this->getDefinitionHints()
        );

        $wildcardDefinitionHints = [];
        foreach ($this->getWildcardHints() as $wildcardHint) {
            $wildcardDefinitionHints[] = $wildcardHint->getDefinitionHints();
        }

        $this->definitionHints = array_merge($this->definitionHints, ...$wildcardDefinitionHints);
    }
}
