<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

interface WildcardHintInterface
{
    /**
     * @return DefinitionHint[]
     * @internal
     */
    public function getDefinitionHints(): array;
}
