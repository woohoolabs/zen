<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Container\Definition\DefinitionInterface;

interface DefinitionHintInterface
{
    /**
     * @param DefinitionHintInterface[] $definitionHints
     * @return DefinitionInterface[]
     * @internal
     */
    public function toDefinitions(array $definitionHints, string $id, bool $isEntryPoint, bool $isAutoloaded, bool $isFileBased): array;
}
