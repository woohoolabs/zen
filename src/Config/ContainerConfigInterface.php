<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config;

use WoohooLabs\Zen\Config\DefinitionHint\DefinitionHint;
use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;

interface ContainerConfigInterface
{
    /**
     * @return EntryPointInterface[]
     */
    public function createEntryPoints(): array;

    /**
     * @return DefinitionHint[]
     */
    public function createDefinitionHints(): array;
}
