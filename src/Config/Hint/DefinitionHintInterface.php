<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Config\EntryPoint\EntryPointInterface;
use WoohooLabs\Zen\Container\Definition\DefinitionInterface;

interface DefinitionHintInterface
{
    /**
     * @internal
     *
     * @param EntryPointInterface[]     $entryPoints
     * @param DefinitionHintInterface[] $definitionHints
     * @return DefinitionInterface[]
     */
    public function toDefinitions(array $entryPoints, array $definitionHints, string $id, bool $isAutoloaded, bool $isFileBased): array;
}
