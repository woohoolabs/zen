<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config;

use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Config\Hint\DefinitionHintInterface;

interface ContainerConfigInterface
{
    /**
     * @return EntryPointInterface[]
     * @internal
     */
    public function createEntryPoints(): array;

    /**
     * @return DefinitionHintInterface[]
     * @internal
     */
    public function createDefinitionHints(): array;
}
