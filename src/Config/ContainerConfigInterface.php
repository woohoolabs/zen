<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config;

use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Config\Hint\DefinitionHintInterface;

interface ContainerConfigInterface
{
    /**
     * @internal
     *
     * @return EntryPointInterface[]
     */
    public function createEntryPoints(): array;

    /**
     * @internal
     *
     * @return DefinitionHintInterface[]
     */
    public function createDefinitionHints(): array;
}
