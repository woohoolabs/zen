<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Definition;

interface DefinitionInterface
{
    /**
     * @return EntrypointInterface[]
     */
    public function getEntryPoints(): array;

    /**
     * @return DefinitionItem[]
     */
    public function getDefinitionItems(): array;
}
