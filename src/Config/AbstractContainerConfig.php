<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config;

use WoohooLabs\Zen\Config\DefinitionHint\DefinitionHint;
use WoohooLabs\Zen\Config\EntryPoint\ClassEntryPoint;
use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Exception\ContainerException;

abstract class AbstractContainerConfig implements ContainerConfigInterface
{
    /**
     * @return array
     */
    abstract protected function getEntryPoints();

    /**
     * @return array
     */
    abstract protected function getDefinitionHints();

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

                if (is_string($entryPoint)) {
                    return new ClassEntryPoint($entryPoint);
                }

                throw new ContainerException("An entry point must be either a string or an EntryPoint object!");
            },
            $this->getEntryPoints()
        );
    }

    /**
     * @return DefinitionHint[]
     */
    public function createDefinitionHints(): array
    {
        return array_map(
            function ($definitionHint): DefinitionHint {
                if ($definitionHint instanceof DefinitionHint) {
                    return $definitionHint;
                }

                if (is_string($definitionHint)) {
                    return new DefinitionHint($definitionHint);
                }

                throw new ContainerException("A definition hint must be either a string or a DefinitionHint object");
            },
            $this->getDefinitionHints()
        );
    }
}
