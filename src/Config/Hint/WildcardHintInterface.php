<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

interface WildcardHintInterface
{
    /**
     * @return DefinitionHintInterface[]
     * @internal
     */
    public function getDefinitionHints(): array;
}
