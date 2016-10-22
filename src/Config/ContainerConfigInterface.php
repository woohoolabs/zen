<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config;

use WoohooLabs\Zen\Config\DefinitionHint\DefinitionHintInterface;
use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;

interface ContainerConfigInterface
{
    /**
     * @return EntryPointInterface[]
     */
    public function createEntryPoints(): array;

    /**
     * @return DefinitionHintInterface[]
     */
    public function createDefinitionHints(): array;
}
