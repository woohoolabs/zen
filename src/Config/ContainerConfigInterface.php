<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Config;

use WoohooLabs\Dicone\Config\DefinitionHint\DefinitionHint;
use WoohooLabs\Dicone\Config\EntryPoint\EntryPointInterface;

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
