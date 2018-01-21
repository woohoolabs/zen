<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config;

use WoohooLabs\Zen\Config\EntryPoint\ClassEntryPoint;
use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Config\Hint\DefinitionHint;
use WoohooLabs\Zen\Config\Hint\DefinitionHintInterface;
use WoohooLabs\Zen\Config\Hint\WildcardHintInterface;
use WoohooLabs\Zen\Exception\ContainerException;

abstract class AbstractContainerConfig implements ContainerConfigInterface
{
    /**
     * @return EntryPointInterface[]
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
     * @return EntryPointInterface[]
     */
    public function createEntryPoints(): array
    {
        return array_map(
            function ($entryPoint): EntryPointInterface {
                if ($entryPoint instanceof EntryPointInterface) {
                    return $entryPoint;
                }

                if (\is_string($entryPoint)) {
                    return new ClassEntryPoint($entryPoint);
                }

                throw new ContainerException("An entry point must be either a string or an EntryPoint object!");
            },
            $this->getEntryPoints()
        );
    }

    /**
     * @return DefinitionHintInterface[]
     */
    public function createDefinitionHints(): array
    {
        $definitionHints = array_map(
            function ($definitionHint): DefinitionHintInterface {
                if ($definitionHint instanceof DefinitionHintInterface) {
                    return $definitionHint;
                }

                if (\is_string($definitionHint)) {
                    return new DefinitionHint($definitionHint);
                }

                throw new ContainerException("A definition hint must be either a string or a DefinitionHint object");
            },
            $this->getDefinitionHints()
        );

        foreach ($this->getWildcardHints() as $wildcardHint) {
            $definitionHints = array_merge($definitionHints, $wildcardHint->getDefinitionHints());
        }

        return $definitionHints;
    }
}
