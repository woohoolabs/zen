<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Config\Hint;

interface WildcardHintInterface
{
    /**
     * @return DefinitionHint[]
     */
    public function getDefinitionHints(): array;
}
