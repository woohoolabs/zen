<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

use WoohooLabs\Zen\Container\Definition\DefinitionInterface;

interface DefinitionHintInterface
{
    /**
     * @param DefinitionHint[] $definitionHints
     * @return DefinitionInterface[]
     */
    public function toDefinitions(array $definitionHints, string $id): array;
}
